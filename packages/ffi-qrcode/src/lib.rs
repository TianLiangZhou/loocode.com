use qrcode::{QrCode, EcLevel};
use image::{Rgba, Pixel as ImagePixel, GenericImageView};
use qrcode::render::{Renderer as QrRenderer, svg::Color, Pixel, unicode::Dense1x2};
use std::ffi::{CStr, CString};
use std::{ptr, slice};
use std::cell::RefCell;
use std::error::Error;
use std::os::raw::{c_int, c_char};
use image::imageops::FilterType;

pub struct QR;

thread_local!{
    static LAST_ERROR: RefCell<Option<Box<dyn Error>>> = RefCell::new(std::option::Option::None);
}

#[derive(Copy, Clone)]
#[repr(C)]
pub struct Cfg {
    // 背景色
    pub bg_color: *const c_char,
    // 前景色
    pub fg_color: *const c_char,
    // 生成图片的时候的文件名
    pub filename: *const c_char,
    // 像素点高度
    pub dimension_w: c_int,
    // 像素点宽度
    pub dimension_h: c_int,
    // logo 标识路径
    pub logo: *const c_char,
    // 过滤logo 透明部分
    pub filter_transparent: c_int,
    // 自动重置logo大小
    pub auto_resize: c_int,
    // 启用留白区域
    pub zone: c_int,
}

pub trait Hex {
    fn to_vec(&self) -> [u8; 4];
}

impl Hex for String {
    fn to_vec(&self) -> [u8; 4] {
        let n = if self.chars().nth(0).unwrap() == '#' { 1 } else { 0 };
        let r = u8::from_str_radix(&self[n+0..n+2],16).unwrap();
        let g = u8::from_str_radix(&self[n+2..n+4],16).unwrap();
        let b = u8::from_str_radix(&self[n+4..n+6],16).unwrap();
        let a = u8::from_str_radix(&self[n+6..n+8],16).unwrap();
        [ r, g, b, a, ]
    }
}


pub trait Setting {
    fn setting(&mut self, cfg: Cfg);
}

impl<'a, P: Pixel> Setting for QrRenderer<'a, P> {
    fn setting(&mut self, cfg: Cfg) {
        let mut w = 0;
        let mut h = 0;
        if cfg.dimension_w > 0 && cfg.dimension_h < 1 {
            w = cfg.dimension_w as u32;
            h = w;
        } else if cfg.dimension_h > 0 && cfg.dimension_w < 1 {
            h = cfg.dimension_h as u32;
            w = h;
        } else if cfg.dimension_h > 0 && cfg.dimension_w > 0 {
            w = cfg.dimension_w as u32;
            h = cfg.dimension_h as u32;
        }
        if w > 0 || h > 0 {
            self.module_dimensions(w, h);
        }
        self.quiet_zone(cfg.zone > 0);
    }
}

pub struct WrapperRenderer<P: CustomRender + Setting> {
    render: P,
    cfg: Cfg
}

impl<P: CustomRender + Setting> WrapperRenderer<P> {

    pub fn new(render: P, cfg: Cfg) -> WrapperRenderer<P> {
        WrapperRenderer{render, cfg}
    }
    pub fn build(&mut self) -> String {
        self.render.setting(self.cfg);
        let (bg, fg) = self.bg_fg();
        self.render.set_color(bg, fg);
        self.render.render(self.cfg)
    }

    fn bg_fg(&self) -> (&'static str, &'static str) {
        let mut bg = "";
        let mut fg = "";
        unsafe {
            if !self.cfg.bg_color.is_null() {
                bg = CStr::from_ptr(self.cfg.bg_color).to_str().expect("utf8 error");
            }
            if !self.cfg.fg_color.is_null() {
                fg = CStr::from_ptr(self.cfg.fg_color).to_str().expect("utf8 error");
            }
        }
        (bg, fg)
    }
}


pub trait CustomRender {
    fn render(&mut self, cfg: Cfg) -> String;
    fn set_color(&mut self, bg: &'static str, fg: &'static str);
}

impl<'a> CustomRender for QrRenderer<'a, char> {
    fn render(&mut self, _cfg: Cfg) -> String {
        self.build()
    }

    fn set_color(&mut self, bg: &str, fg: &str) {
        if bg.len() > 0 {
            self.light_color(bg.as_bytes()[0] as char);
        }
        if fg.len() > 0 {
            self.dark_color(fg.as_bytes()[0] as char);
        }
    }
}
impl<'a> CustomRender for QrRenderer<'a, &str> {
    fn render(&mut self, _cfg: Cfg) -> String {
        self.build()
    }
    fn set_color(&mut self, bg: &'static str, fg: &'static str) {
        if bg.len() > 0 {
            self.light_color(bg);
        }
        if fg.len() > 0 {
            self.dark_color(fg);
        }
    }
}
impl<'a> CustomRender for QrRenderer<'a, Color<'a>> {
    fn render(&mut self, _cfg: Cfg) -> String {
        self.build()
    }
    fn set_color(&mut self, bg: &'static str, fg: &'static str) {
        if bg.starts_with('#') && (bg.len() == 7 || bg.len() == 4) {
            self.light_color(Color(bg));
        }
        if fg.starts_with('#') && (fg.len() == 7 || fg.len() == 4) {
            self.dark_color(Color(fg));
        }
    }
}
impl<'a> CustomRender for QrRenderer<'a, Dense1x2> {
    fn render(&mut self, _cfg: Cfg) -> String {
        self.build()
    }
    fn set_color(&mut self, bg: &'static str, fg: &'static str) {
        if bg.len() > 0 {
            self.light_color(Dense1x2::Light);
        }
        if fg.len() > 0 {
            self.dark_color(Dense1x2::Dark);
        }
    }
}

impl<'a> CustomRender for QrRenderer<'a, Rgba<u8>> {

    fn render(&mut self, cfg: Cfg) -> String {
        let mut image = self.build();
        let mut name = "";
        let mut logo = "";
        unsafe {
            if !cfg.filename.is_null() {
                name = CStr::from_ptr(cfg.filename).to_str().unwrap();
            }
            if !cfg.logo.is_null() {
                logo = CStr::from_ptr(cfg.logo).to_str().unwrap();
            }
        }
        let w = image.width();
        let h = image.height();
        if logo.is_empty() {
            match image.save(name) {
                Err(e) => {
                    update_last_error(e);
                }
                _ => {}
            }
            return String::from(name);
        }
        let mut logo_image = match image::open(logo) {
            Ok(dy) => dy,
            Err(e) => {
                update_last_error(e);
                return String::new();
            }
        };
        if cfg.auto_resize > 0 && logo_image.width() > w * 3 / 10 {
            logo_image = logo_image.resize( w * 3 / 10, h * 3 / 10, FilterType::Nearest);
        }
        let logo_image_rgba = logo_image.to_rgba8();
        let logo_w = logo_image_rgba.width();
        let logo_h  = logo_image_rgba.height();
        let start_x = (w - logo_w) / 2;
        let start_y = (h - logo_h) / 2;
        for (x, y, p) in image.enumerate_pixels_mut() {
            if x >= start_x && y >= start_y && x - start_x <= logo_w - 1 && y - start_y <= logo_h - 1 {
                let lp = *logo_image_rgba.get_pixel(x - start_x, y - start_y);
                if cfg.filter_transparent > 0 && lp.0 == [255,255,255, 0] {
                    continue;
                }
                *p = lp
            }
        }
        return match image.save(name) {
            Err(e) => {
                update_last_error(e);
                String::new()
            }
            _ => {
                String::from(name)
            }
        }
    }
    fn set_color(&mut self, bg: &'static str, fg: &'static str) {
        let mut b = bg.to_string();
        let mut f = fg.to_string();
        if b.len() > 0 && b.starts_with("#") {
            b = bg[1..].to_string();
        }
        if f.len() > 0 && f.starts_with("#"){
            f = fg[1..].to_string();
        }

        fn repeat(mut s: String, r_str: &str) -> String {
            match s.len() {
                d if d < 8 => {
                    s.push_str(r_str.repeat(8 - d).as_str());
                    s
                },
                d if d > 8 => {
                    s[0..8].to_string()
                },
                _ => s
            }
        }
        if b.len() > 0 {
            let c_bg = repeat(b, "F").to_vec();
            self.light_color(*Rgba::from_slice(&c_bg));
        }
        if f.len() > 0 {
            let c_fg  = repeat(f, "F").to_vec();
            self.dark_color(*Rgba::from_slice(&c_fg));
        }
    }
}

#[no_mangle]
pub unsafe extern "C" fn create(str: *const c_char) -> *mut QR {
    if str.is_null() {
        return ptr::null_mut();
    }
    let raw = CStr::from_ptr(str);
    let data = match raw.to_str() {
        Ok(s) => s,
        Err(e) => {
            update_last_error(e);
            return ptr::null_mut()
        }
    };
    let qrcode = match QrCode::with_error_correction_level(data, EcLevel::H) {
        Ok(qrcode) => qrcode,
        Err(err) => {
            update_last_error(err);
            return ptr::null_mut();
        }
    };
    Box::into_raw(Box::new(qrcode)) as *mut QR
}



#[no_mangle]
pub unsafe extern "C" fn image(qr: *mut QR, cfg: *mut Cfg) -> *mut c_char {
    let qr_code = qr as *mut QrCode;
    let renderer = (*qr_code).render::<Rgba<u8>>();
    build::<QrRenderer<Rgba<u8>>>(renderer, cfg)
}

#[no_mangle]
pub unsafe extern "C" fn svg(qr: *mut QR, cfg: *mut Cfg) -> *mut c_char {
    let qr_code = qr as *mut QrCode;
    let renderer = (*qr_code).render::<Color>();
    build::<QrRenderer<Color>>(renderer, cfg)
}

#[no_mangle]
pub unsafe extern "C" fn character(qr: *mut QR, cfg: *mut Cfg) -> *mut c_char {
    let qr_code = qr as *mut QrCode;
    let renderer = (*qr_code).render::<char>();
    build::<QrRenderer<char>>(renderer, cfg)
}

#[no_mangle]
pub unsafe extern "C" fn unicode(qr: *mut QR, cfg: *mut Cfg) -> *mut c_char {
    let qr_code = qr as *mut QrCode;
    let renderer = (*qr_code).render::<Dense1x2>();
    build::<QrRenderer<Dense1x2>>(renderer, cfg)
}


unsafe fn build<T: CustomRender + Setting>(render: T, cfg: *mut Cfg) -> *mut c_char {
    let mut wrapper = WrapperRenderer::new(render, cfg.read());
    let image = wrapper.build();
    return CString::new(image).unwrap().into_raw()
}

#[no_mangle]
pub extern "C" fn free_image(ptr: *mut c_char) {
    unsafe {
        if ptr.is_null() {
            // No data there, already freed probably.
            return;
        }
        // Here we reclaim ownership of the data the pointer points to, to free the memory properly.
        CString::from_raw(ptr);
    }
}


/// Destroy a `Response` once you are done with it.
#[no_mangle]
pub extern "C" fn free_qrcode(res: *mut QR) {
    if !res.is_null() {
        unsafe { drop(Box::from_raw(res as *mut QrCode)); }
    }
}

#[no_mangle]
pub extern "C" fn last_error_length() -> c_int {
    LAST_ERROR.with(|prev| match *prev.borrow() {
        Some(ref err) => err.to_string().len() as c_int + 1,
        None => 0,
    })
}

#[no_mangle]
pub unsafe extern "C" fn last_error_message(buffer: *mut c_char, length: c_int) -> c_int {
    if buffer.is_null() {
        // println!("Null pointer passed into last_error_message() as the buffer");
        return -1;
    }

    let last_error = match take_last_error() {
        Some(err) => err,
        None => return 0,
    };

    let error_message = last_error.to_string();

    let buffer = slice::from_raw_parts_mut(buffer as *mut u8, length as usize);

    if error_message.len() >= buffer.len() {
        // println!("Buffer provided for writing the last error message is too small.");
        // println!(
        //     "Expected at least {} bytes but got {}",
        //     error_message.len() + 1,
        //     buffer.len()
        // );
        return -1;
    }
    ptr::copy_nonoverlapping(
        error_message.as_ptr(),
        buffer.as_mut_ptr(),
        error_message.len(),
    );
    // Add a trailing null so people using the string as a `char *` don't
    // accidentally read into garbage.
    buffer[error_message.len()] = 0;
    error_message.len() as c_int
}


/// Update the most recent error, clearing whatever may have been there before.
fn update_last_error<E: Error + 'static>(err: E) {
    // println!("Setting LAST_ERROR: {}", err);
    {
        // Print a pseudo-backtrace for this error, following back each error's
        // cause until we reach the root error.
        let mut cause = err.source();
        while let Some(parent_err) = cause {
            // println!("Caused by: {}", parent_err);
            cause = parent_err.source();
        }
    }
    LAST_ERROR.with(|prev| {
        *prev.borrow_mut() = Some(Box::new(err));
    });
}

/// Retrieve the most recent error, clearing it in the process.
fn take_last_error() -> Option<Box<dyn Error>> {
    LAST_ERROR.with(|prev| prev.borrow_mut().take())
}

#[cfg(test)]
mod tests {

    #[test]
    fn it_works() {

    }
}


