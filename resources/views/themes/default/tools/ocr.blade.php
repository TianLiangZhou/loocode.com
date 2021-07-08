@extends("default.tools.tool")
@section("body")
    <div class="flex my-5">
        <label class="text-gray-700 dark:text-gray-400">图片</label>
        <div class="flex-1 ml-4">
            <input accept="image/*" placeholder="上传图片" name="logo" id="logo" value="选择图片" type="file" class="ml-2 mt-0 block px-0.5 py-2 text-sm dark:bg-gray-900" />
            <span class="text-sm text-gray-400">限制最大为2M</span>
        </div>
    </div>
    <div class="flex my-5">
        <img alt="原图" id="image" width="320" />
    </div>
    <div class="flex my-5 justify-center">
        <button type="button" class="bg-red-500 text-white rounded-md px-3 py-1 text-xl font-bold" @click.prevent="convert()">生成</button>
    </div>
    <div class="flex my-5">
        <label class="text-gray-700 dark:text-gray-400">识别结果</label>
        <div class="flex-1 ml-4">
            <textarea rows="10" x-model="data" class="placeholder-gray-500 mt-0 block w-full p-2 text-sm border-2 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 focus:ring-0 focus:border-red-500"
                      placeholder="这里是识别之后结果">
            </textarea>
        </div>
    </div>
@endsection

@section("footer_js")
    @parent
    <script type="text/javascript">
        document.getElementById('logo').onchange = function () {
            document.getElementById('image').src = URL.createObjectURL(this.files[0])
        }
    </script>
@endsection
