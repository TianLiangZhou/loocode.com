use image::{Rgba, RgbaImage, Pixel, GenericImageView};
use qrcode::{QrCode, EcLevel};
use image::imageops::FilterType;
use qrcode::render::svg;
use qrcode::render::svg::Color;
use std::ffi::{CString, CStr};
use std::{ptr, slice};

fn main() {


    let cstr = CString::new("xx").expect("error utf8").as_ptr();
    let f_cstr = CString::new("yy").expect("error utf8").as_ptr();
    let logo = CString::new("").expect("error utf8").as_ptr();
    let filename = CString::new("test.png").expect("error utf8").as_ptr();
    let cfg = &mut ffi_qrcode::Cfg {
        bg_color: cstr,
        fg_color: f_cstr,
        filename,
        dimension_w: 0,
        dimension_h: 0,
        logo,
        filter_transparent: 0,
        auto_resize: 0,
        zone: 0
    };

    // let cc_str = unsafe { CStr::from_ptr(cstr) }.to_str().unwrap();
    // println!("cc_str {}", cc_str);
    let code = QrCode::with_error_correction_level(b"01234567", EcLevel::H).unwrap();
    let svg = code.render::<Color>();
    let mut render = ffi_qrcode::WrapperRenderer::new(svg, cfg.to_owned());

    println!("{}", render.build());


    std::process::exit(1);
    let code = QrCode::with_error_correction_level(b"01234567", EcLevel::H).unwrap();
    let char = code.render::<char>()
        .quiet_zone(false)
        .module_dimensions(3, 1)
        .build();

    println!("{}", char);

    // Encode some data into bits.
    let code = QrCode::with_error_correction_level(b"01234567", EcLevel::H).unwrap();


    let red = *Rgba::from_slice(&[255, 255, 255, 255]);
    // Render the bits into an image.
    let mut image = code.render::<Rgba<u8>>().
        // module_dimensions(1, 1).
        light_color(red).
        build();
    let pixels_mut = image.enumerate_pixels_mut();

    let mut logo = image::open("examples/pyq.png").unwrap();

    println!("{:#?}", logo.get_pixel(1,1));

    let w = image.width();
    let h = image.height();

    if logo.width() > w * 3 / 10 {
        logo = logo.resize( w * 3 / 10, h * 3 / 10, FilterType::Triangle);
    }
    let mut logo_image = logo.to_rgba8();
    let logo_w = logo_image.width();
    let logo_h  = logo_image.height();
    let start_x = (w - logo_w) / 2;
    let start_y = (h - logo_h) / 2;

    for (x, y, p) in image.enumerate_pixels_mut() {
        if x >= start_x && y >= start_y && x - start_x <= logo_w - 1 && y - start_y <= logo_h - 1 {
            // println!("{}, {}", x - (wr - rwr), y - (hr - rhr));
            let mut lp = *logo_image.get_pixel(x - start_x, y - start_y);
            if lp.0 == [255,255,255, 0] {
                lp.0 = [0,0,0,255];
            }
            // if lp.0[3] < 4 {
            //     continue;
            // }
            *p = lp;
            println!("{:?}", p);
        }
    }
    // Save the image.
    image.save("examples/qrcode.png").unwrap();
}