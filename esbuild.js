const prismjsPlugin=require('esbuild-plugin-prismjs');
(async () => {
  const path = require('path')
  const esbuild = require('esbuild')
  const __dirname = path.resolve();

  const options = {
    logLevel: 'info',
    entryPoints: [
      'assets/js/prismjs.js',
      'assets/js/json-to-class.js',
      'assets/js/json-beautiful.js',
      'assets/js/base64.js',
      'assets/js/image-base64.js',
      'assets/js/crontab.js',
      'assets/js/table-data-generator.js',
      'assets/css/prism.css',
      'assets/js/json-to-protobuf.js',
    ],
    bundle: true,
    outdir: 'public/assets',
    outbase: 'assets',
    write: true,
    allowOverwrite: true,
    sourcemap: process.argv.includes('--dev'),
    minify: !process.argv.includes('--dev'),
    metafile: process.argv.includes('--analyze'),
    loader: {
      '.gif': 'file',
      '.eot': 'file',
      '.ttf': 'file',
      '.svg': 'file',
      '.woff': 'file',
      '.woff2': 'file',
    },
    target: ['es2020'],
    plugins: [
      prismjsPlugin.prismjsPlugin({
        inline: true,
        languages: ['typescript', 'javascript', 'css', 'markup', 'bash', 'php', 'go', 'java', 'sh', 'shell', 'html', 'sql', 'json', 'py', 'nginx', 'kt', 'rust', 'ini', 'py', 'jsx', 'json', 'json5', 'scss', 'less', 'c', 'cpp', 'cs', 'twig', 'toml', 'git', 'protobuf'],
        plugins: [
          'autoloader',
          'line-highlight',
          'line-numbers',
          'show-language',
          'copy-to-clipboard',
          'toolbar',
        ],
        theme: 'okaidia',
        css: true,
      }),
    ],
  };

  let ctx = null;
  if (process.argv.includes("--watch")) {
    ctx = await esbuild.context(options)
  } else {
    ctx = await esbuild.build(options)
  }
  if (process.argv.includes('--analyze')) {
    const text = await esbuild.analyzeMetafile(ctx.metafile)
    console.log(text);
  }
  if (process.argv.includes('--watch')) {
    await ctx.watch();
    let {port, host} = await ctx.serve({

    });
  }
})().catch((e) => console.error(e) || process.exit(1));
