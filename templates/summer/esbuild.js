const prismjsPlugin=require('esbuild-plugin-prismjs');
(async () => {
  const path = require('path')
  const esbuild = require('esbuild')
  const __dirname = path.resolve();

  const options = {
    logLevel: 'info',
    entryPoints: [
      'dev/js/summer.js',
      'dev/js/prismjs.js',
      'dev/js/json-to-go.js',
      'dev/css/summer.css',
    ],
    bundle: true,
    outdir: './',
    assetNames: 'css/fonts/[name]',
    outbase: 'dev',
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
    target: ['chrome58', 'firefox57', 'safari11', 'edge95'],
    plugins: [
      prismjsPlugin.prismjsPlugin({
        inline: true,
        languages: ['typescript', 'javascript', 'css', 'markup', 'bash', 'php', 'go', 'java', 'sh', 'shell', 'html', 'sql', 'json', 'py', 'nginx', 'kt'],
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
