@extends("default.tools.tool")
@section("body")
    <div class="flex">
        <label class="text-gray-700">模式</label>
        <div class="flex-1 ml-4">
            <div class="flex items-center" x-init="mode = 1">
                <label><input name="mode" x-model="mode" value="1"  type="radio"> 图片</label>
                <label class="mx-2"><input x-model="mode" name="mode" value="2" type="radio"> SVG</label>
                <label><input name="mode" x-model="mode" value="3" type="radio"> 字符</label>
            </div>
        </div>
    </div>
    <div class="flex my-5" x-show="mode == 1 || mode == 2">
        <label class="text-gray-700">颜色</label>
        <div class="flex-1 ml-4">
            <div class="flex items-center">
                <input placeholder="背景" type="color" class="ml-2 mt-0 block px-0.5 py-2 text-sm" name="bg_color" value="" maxlength="7" minlength="7" />
            </div>
            <div class="flex items-center mt-2">
                <input placeholder="前景" type="color" class="ml-2 mt-0 block px-0.5 py-2 text-sm" name="fg_color" value="" maxlength="7" minlength="7" />
            </div>
        </div>
    </div>
    <div class="flex my-5" x-show="mode == 1">
        <label class="text-gray-700">Logo</label>
        <div class="flex-1 ml-4">
            <input placeholder="上传logo" name="logo" value="选择logo" type="file" class="ml-2 mt-0 block px-0.5 py-2 text-sm" />
            <span class="text-sm text-gray-400">限制最大为2M</span>
        </div>
    </div>
    <div class="flex my-5" x-show="mode == 3">
        <label class="text-gray-700">字符</label>
        <div class="flex-1 ml-4">
            <input placeholder="二维码字符" class="placeholder-gray-500 mt-0 block px-0.5 py-2 text-sm border-0 border-b border-gray-200 dark:bg-gray-800 dark:text-gray-50 focus:ring-0 focus:border-red-500" name="char" value="" maxlength="1" minlength="1" />
        </div>
    </div>
    <div class="flex my-5">
        <label class="text-gray-700">文本</label>
        <div class="flex-1 ml-4">
            <textarea name="text" rows="10" class="placeholder-gray-500 mt-0 block w-full p-2 text-sm border-2 border-gray-200 dark:bg-gray-800 dark:text-gray-50 focus:ring-0 focus:border-red-500"
                      placeholder="输入你需要生成二维码的文本"></textarea>
        </div>
    </div>
    <div class="flex my-5 justify-center">
        <button type="button" class="bg-red-500 text-white rounded-md px-3 py-1 text-xl font-bold" @click.prevent="convert()">生成</button>
    </div>
    <div class="flex my-5">
        <label class="text-gray-700">二维码</label>
        <div class="flex-1 ml-4" x-html="data">
        </div>
    </div>
@endsection
