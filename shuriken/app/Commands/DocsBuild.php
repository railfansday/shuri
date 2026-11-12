<?php

namespace App\Commands;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use LaravelZero\Framework\Commands\Command;

class DocsBuild extends Command
{
    protected $signature = 'docs:build {--deploy : Deploy to Cloudflare Pages after build}';
    protected $description = 'Generate HTML documentation from Markdown files';

    const NAV = [
        ['label' => '🏠 Beranda',          'href' => 'index.html',            'depth' => 0],
        ['label' => 'Memulai',             'href' => null,                    'depth' => 0],
        ['label' => 'Instalasi',           'href' => 'installation.html',     'depth' => 1],
        ['label' => 'Quick Start',         'href' => 'quick-start.html',      'depth' => 1],
        ['label' => 'AI & Provider',       'href' => null,                    'depth' => 0],
        ['label' => 'AI Providers',        'href' => 'ai-providers.html',     'depth' => 1],
        ['label' => 'Panduan Biaya AI',    'href' => 'ai-cost-guide.html',    'depth' => 1],
        ['label' => 'Export',              'href' => null,                    'depth' => 0],
        ['label' => 'Platform Export',     'href' => 'export-platforms.html', 'depth' => 1],
        ['label' => 'HTML Themes',         'href' => 'html-themes.html',      'depth' => 1],
        ['label' => 'Monetisasi',          'href' => null,                    'depth' => 0],
        ['label' => 'Panduan Adsterra',    'href' => 'monetization.html',     'depth' => 1],
        ['label' => 'Referensi Command',   'href' => null,                    'depth' => 0],
        ['label' => 'image:scrape',        'href' => 'commands/image-scrape.html',   'depth' => 1],
        ['label' => 'image:export',        'href' => 'commands/image-export.html',   'depth' => 1],
        ['label' => 'clear:data',          'href' => 'commands/clear-data.html',     'depth' => 1],
        ['label' => 'token',               'href' => 'commands/token.html',          'depth' => 1],
        ['label' => 'prompt',              'href' => 'commands/prompt.html',         'depth' => 1],
        ['label' => 'site',                'href' => 'commands/site.html',           'depth' => 1],
        ['label' => 'post:publish',        'href' => 'commands/post-publish.html',   'depth' => 1],
        ['label' => 'site:deploy',         'href' => 'commands/site-deploy.html',    'depth' => 1],
        ['label' => 'mcp:serve',           'href' => 'commands/mcp-serve.html',      'depth' => 1],
        ['label' => 'Case Study',           'href' => null,                    'depth' => 0],
        ['label' => 'Bisnis Lokal (Umroh)','href' => 'case-study-bisnis-lokal.html', 'depth' => 1],
        ['label' => 'Auto-Blog Resep + AdSense', 'href' => 'case-study-resep-adsense.html', 'depth' => 1],
        ['label' => 'Affiliate Shopee (Indoleads)', 'href' => 'case-study-affiliate-shopee.html', 'depth' => 1],
        ['label' => 'Nickname Tier 1 + Adsterra',  'href' => 'case-study-nickname-tier1.html',    'depth' => 1],
        ['label' => 'Niche "Map Of" Tier 1',       'href' => 'case-study-map-of.html',            'depth' => 1],
        ['label' => 'Niche PFP Tier 1',            'href' => 'case-study-pfp.html',               'depth' => 1],
        ['label' => 'Printable Worksheets',        'href' => 'case-study-printable.html',         'depth' => 1],
        ['label' => 'Lainnya',             'href' => null,                    'depth' => 0],
        ['label' => 'Custom Templates',    'href' => 'custom-templates.html', 'depth' => 1],
        ['label' => 'FAQ & Tips',          'href' => 'faq.html',              'depth' => 1],
        ['label' => 'Konfigurasi',         'href' => 'configuration.html',    'depth' => 1],
    ];

    public function handle()
    {
        $mdDir   = base_path('docs/md');
        $htmlDir = base_path('docs/html');

        $environment = new Environment(['html_input' => 'strip', 'allow_unsafe_links' => false, 'heading_permalink' => ['symbol' => '']]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new HeadingPermalinkExtension());
        $converter = new MarkdownConverter($environment);

        $files = $this->getMdFiles($mdDir);

        foreach ($files as $mdPath) {
            $this->task(str_replace($mdDir . '/', '', $mdPath), function () use ($mdPath, $mdDir, $htmlDir, $converter) {
                $relative  = str_replace($mdDir . '/', '', $mdPath);
                $htmlRel   = preg_replace('/\.md$/', '.html', $relative);
                $htmlRel   = str_replace('README.html', 'index.html', $htmlRel);
                $htmlPath  = $htmlDir . '/' . $htmlRel;

                if (!is_dir(dirname($htmlPath))) {
                    mkdir(dirname($htmlPath), 0755, true);
                }

                $md      = file_get_contents($mdPath);
                $content = $converter->convert($md)->getContent();

                // Konversi link .md → .html untuk navigasi antar halaman
                $content = preg_replace('/href="([^"]+)\.md"/', 'href="$1.html"', $content);

                $depth   = substr_count($htmlRel, '/');
                $root    = str_repeat('../', $depth);
                $title   = $this->extractTitle($md);
                $nav     = $this->buildNav($htmlRel, $root);
                $html    = $this->buildPage($title, $content, $nav, $root);

                file_put_contents($htmlPath, $html);
            });
        }

        $this->info("\nDone! Buka docs/html/index.html di browser.");

        if ($this->option('deploy')) {
            $this->deployToCloudflare();
        }
    }

    private function deployToCloudflare(): void
    {
        $this->newLine();
        $this->task('Deploy ke shuriken-docs.dojo.cc', function () {
            $process = new \Symfony\Component\Process\Process(
                ['npx', 'wrangler', 'pages', 'deploy', 'docs/html', '--project-name=shuriken-docs'],
                base_path()
            );
            $process->setTimeout(120);
            $process->mustRun();
        });
        $this->info('✓ Docs live di https://shuriken-docs.dojo.cc');
    }

    private function getMdFiles(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'md') {
                $files[] = $file->getPathname();
            }
        }
        sort($files);
        return $files;
    }

    private function extractTitle(string $md): string
    {
        if (preg_match('/^#\s+(.+)$/m', $md, $m)) {
            return trim($m[1]);
        }
        return 'Shuriken Docs';
    }

    private function buildNav(string $currentHtmlRel, string $root): string
    {
        $html = '';
        foreach (self::NAV as $item) {
            if ($item['href'] === null) {
                $html .= '<div class="nav-section">' . htmlspecialchars($item['label']) . '</div>';
            } else {
                $active = ($item['href'] === $currentHtmlRel) ? ' active' : '';
                $href   = $root . $item['href'];
                $indent = $item['depth'] === 1 ? ' style="padding-left:1.2rem"' : '';
                $html  .= '<a href="' . $href . '" class="nav-link' . $active . '"' . $indent . '>'
                        . htmlspecialchars($item['label']) . '</a>';
            }
        }
        return $html;
    }

    private function buildPage(string $title, string $content, string $nav, string $root): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$title} — Shuriken</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;font-size:15px;color:#222;background:#f5f5f5;line-height:1.7}
a{color:#c0392b;text-decoration:none}
a:hover{text-decoration:underline}
.layout{display:flex;min-height:100vh}
.sidebar{width:240px;flex-shrink:0;background:#1e1e2e;padding:1rem 0;overflow-y:auto;position:sticky;top:0;height:100vh}
.sidebar-logo{padding:.75rem 1.2rem 1rem;font-size:1.1rem;font-weight:700;color:#fff;border-bottom:1px solid #333}
.sidebar-logo span{color:#e74c3c}
.nav-section{padding:.7rem 1.2rem .2rem;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#666}
.nav-link{display:block;padding:.35rem 1.2rem;color:#bbb;font-size:.88rem;transition:background .15s,color .15s}
.nav-link:hover{background:#2a2a3e;color:#fff;text-decoration:none}
.nav-link.active{color:#e74c3c;background:#2a2a3e;font-weight:600}
.content{flex:1;min-width:0;padding:2.5rem 3rem;max-width:860px}
h1{font-size:1.8rem;margin-bottom:1.2rem;padding-bottom:.5rem;border-bottom:2px solid #e74c3c;color:#111}
h2{font-size:1.25rem;margin:2rem 0 .6rem;color:#111}
h3{font-size:1rem;margin:1.5rem 0 .4rem;color:#333}
p{margin:.6rem 0}
ul,ol{margin:.5rem 0 .5rem 1.5rem}
li{margin:.25rem 0}
table{border-collapse:collapse;width:100%;margin:1rem 0;font-size:.9rem;display:block;overflow-x:auto;-webkit-overflow-scrolling:touch}
th{background:#1e1e2e;color:#fff;padding:.5rem .8rem;text-align:left}
td{padding:.45rem .8rem;border-bottom:1px solid #e0e0e0}
tr:hover td{background:#fafafa}
pre{background:#1e1e2e;color:#f8f8f2;padding:1rem 1.2rem;border-radius:6px;overflow-x:auto;margin:1rem 0;font-size:.85rem;line-height:1.5}
code{font-family:'SFMono-Regular',Consolas,monospace;font-size:.88em}
p code,li code,td code{background:#eee;padding:.1em .35em;border-radius:3px;color:#c0392b}
blockquote{border-left:3px solid #e74c3c;padding:.5rem 1rem;color:#555;background:#fff8f8;margin:1rem 0}
hr{border:none;border-top:1px solid #ddd;margin:1.5rem 0}
img{max-width:100%;height:auto;border-radius:6px;border:1px solid #e0e0e0;margin:.5rem 0}
.subscribe-box{margin-top:3rem;padding:1.5rem;background:#fff8f8;border:1px solid #f5c6c6;border-radius:8px;text-align:center}
.subscribe-label{font-size:.9rem;font-weight:600;color:#c0392b;margin-bottom:.75rem}
.footer{margin-top:1.5rem;padding-top:1rem;border-top:1px solid #e0e0e0;font-size:.82rem;color:#999}
.footer a{color:#999}
.footer a:hover{color:#c0392b}
.topbar{display:none;align-items:center;gap:.75rem;background:#1e1e2e;padding:.75rem 1rem;position:sticky;top:0;z-index:100}
.topbar-logo{font-size:1rem;font-weight:700;color:#fff}
.topbar-logo span{color:#e74c3c}
.burger{background:none;border:none;cursor:pointer;padding:.25rem;display:flex;flex-direction:column;gap:5px}
.burger span{display:block;width:22px;height:2px;background:#bbb;border-radius:2px;transition:background .2s}
.burger:hover span{background:#fff}
.drawer{position:fixed;inset:0;z-index:200;display:none}
.drawer.open{display:block}
.drawer-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.5)}
.drawer-panel{position:absolute;left:0;top:0;bottom:0;width:260px;background:#1e1e2e;overflow-y:auto;padding:1rem 0;transform:translateX(-100%);transition:transform .25s ease}
.drawer.open .drawer-panel{transform:translateX(0)}
.drawer-close{display:flex;align-items:center;justify-content:space-between;padding:.5rem 1.2rem 1rem;border-bottom:1px solid #333;margin-bottom:.25rem}
.drawer-close .logo{font-size:1rem;font-weight:700;color:#fff}
.drawer-close .logo span{color:#e74c3c}
.drawer-close button{background:none;border:none;color:#888;font-size:1.3rem;cursor:pointer;line-height:1}
.drawer-close button:hover{color:#fff}
@media(max-width:700px){.sidebar{display:none}.content{padding:1.5rem}.topbar{display:flex}}
</style>
</head>
<body>
<div class="topbar">
  <button class="burger" onclick="openDrawer()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
  <div class="topbar-logo">⚡ <span>Shuriken</span></div>
</div>
<div class="drawer" id="drawer" onclick="handleDrawerClick(event)">
  <div class="drawer-backdrop"></div>
  <div class="drawer-panel">
    <div class="drawer-close">
      <div class="logo">⚡ <span>Shuriken</span></div>
      <button onclick="closeDrawer()" aria-label="Tutup">✕</button>
    </div>
    {$nav}
  </div>
</div>
<div class="layout">
<aside class="sidebar">
  <div class="sidebar-logo">⚡ <span>Shuriken</span></div>
  {$nav}
</aside>
<main class="content">
{$content}
<div class="subscribe-box">
  <p class="subscribe-label">📬 Tips & update terbaru dari Internet Marketing Dojo</p>
  <iframe src="https://imdojo.substack.com/embed" width="480" height="320" style="border:1px solid #EEE;background:white;max-width:100%" frameborder="0" scrolling="no"></iframe>
</div>
<div class="footer"><a href="https://dojo.cc/shuriken" target="_blank">Shuriken</a> by <a href="https://imdojo.substack.com" target="_blank">Internet Marketing Dojo</a></div>
</main>
</div>
<script>
function openDrawer(){document.getElementById('drawer').classList.add('open');document.body.style.overflow='hidden'}
function closeDrawer(){document.getElementById('drawer').classList.remove('open');document.body.style.overflow=''}
function handleDrawerClick(e){if(e.target.classList.contains('drawer-backdrop'))closeDrawer()}
document.querySelectorAll('#drawer .nav-link').forEach(function(a){a.addEventListener('click',closeDrawer)})
</script>
<script type="text/javascript">window.\$crisp=[];window.CRISP_WEBSITE_ID="08b4d506-ee5f-49d8-ab6a-b3d3196bc5c0";window.\$crisp.push(["config","position:reverse",[false]]);(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
</body>
</html>
HTML;
    }
}
