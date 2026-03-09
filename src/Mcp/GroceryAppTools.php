<?php

namespace App\Mcp;

use Mcp\Capability\Attribute\McpResource;
use Mcp\Capability\Attribute\McpTool;

/**
 * P3 — MCP Apps tools.
 *
 * These tools return data that is rendered as interactive widgets
 * by the host (Claude Desktop). The widget HTML is served via the
 * McpResource at ui://grocery/widget.html.
 */
class GroceryAppTools
{
    private const RESOURCE_URI = 'ui://grocery/widget.html';

    /* ── Resource: widget HTML ─────────────────────────── */

    #[McpResource(
        uri: self::RESOURCE_URI,
        name: 'Grocery-Widget',
        mimeType: 'text/html;profile=mcp-app',
        meta: ['ui' => ['csp' => ['resourceDomains' => ['https://cdn.jsdelivr.net']]]],
    )]
    public function getWidget(): string
    {
        return $this->getWidgetHtml();
    }

    /* ── Tool: suggest-bread (product picker) ──────────── */

    #[McpTool(
        name: 'suggest-bread',
        description: 'When user asks to add a generic item like "pain" or "bread", show an interactive product picker with specific bread types. The user picks their preferred option in the widget. Use this INSTEAD of add-item when the item is a broad category. IMPORTANT: An interactive picker widget is displayed — do NOT repeat the options in text.',
        meta: [
            'ui' => ['resourceUri' => self::RESOURCE_URI],
            'ui/resourceUri' => self::RESOURCE_URI,
        ],
    )]
    public function suggestBread(int $listId, string $listName): string
    {
        return json_encode([
            'mode' => 'picker',
            'category' => 'pain',
            'listId' => (string) $listId,
            'listName' => $listName,
            'suggestions' => [
                [
                    'name' => 'Baguette tradition',
                    'description' => 'Croustillante, mie alvéolée et dorée',
                    'price' => '1,20 €',
                    'emoji' => '🥖',
                ],
                [
                    'name' => 'Pain de campagne',
                    'description' => 'Mie dense, croûte épaisse et rustique',
                    'price' => '2,80 €',
                    'emoji' => '🍞',
                ],
                [
                    'name' => 'Pain complet',
                    'description' => 'Farine complète, riche en fibres',
                    'price' => '2,50 €',
                    'emoji' => '🌾',
                ],
                [
                    'name' => 'Pain aux céréales',
                    'description' => 'Graines de lin, tournesol, sésame',
                    'price' => '3,10 €',
                    'emoji' => '🥯',
                ],
            ],
        ], JSON_THROW_ON_ERROR);
    }

    /* ── Tool: test-mcp-apps (demo widget) ─────────────── */

    #[McpTool(
        name: 'test-mcp-apps',
        description: 'Display a test MCP Apps widget to demonstrate that the MCP server can return rich UI components rendered by the host (Claude).',
        meta: [
            'ui' => ['resourceUri' => self::RESOURCE_URI],
            'ui/resourceUri' => self::RESOURCE_URI,
        ],
    )]
    public function testMcpApps(): string
    {
        return json_encode([
            'mode' => 'test',
            'title' => 'MCP Apps — Test Widget',
            'message' => 'Ce widget a été généré par le serveur MCP Symfony et rendu par le host (Claude Desktop).',
            'serverInfo' => [
                'framework' => 'Symfony 8',
                'language' => 'PHP 8.4',
                'paradigm' => 'P3 — MCP Apps',
                'transport' => 'stdio',
            ],
        ], JSON_THROW_ON_ERROR);
    }

    /* ── Widget HTML ───────────────────────────────────── */

    private function getWidgetHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        padding: 16px;
        color: #1c1917;
        background: transparent;
    }

    /* ── Picker mode ── */
    .picker-header { margin-bottom: 12px; }
    .picker-header h2 { font-size: 1rem; font-weight: 600; }
    .picker-header p { font-size: 0.8rem; color: #78716c; margin-top: 2px; }
    .picker-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .picker-card {
        background: #fafaf9;
        border: 1px solid #e7e5e4;
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .picker-card:hover {
        border-color: #16a34a;
        background: #f0fdf4;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(22, 163, 74, 0.12);
    }
    .picker-card.selected {
        border-color: #16a34a;
        background: #dcfce7;
    }
    .picker-emoji { font-size: 1.5rem; margin-bottom: 4px; }
    .picker-name { font-weight: 600; font-size: 0.85rem; }
    .picker-desc { font-size: 0.75rem; color: #78716c; margin-top: 2px; }
    .picker-price { font-size: 0.8rem; font-weight: 600; color: #16a34a; margin-top: 6px; }
    .picker-status {
        text-align: center;
        margin-top: 12px;
        font-size: 0.8rem;
        color: #16a34a;
        font-weight: 500;
    }

    /* ── Test mode ── */
    .test-card {
        background: linear-gradient(135deg, #faf5ff, #f5f3ff);
        border: 1px solid #c4b5fd;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .test-badge {
        display: inline-block;
        background: #7c3aed;
        color: white;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }
    .test-card h2 { font-size: 1.1rem; margin-bottom: 8px; }
    .test-card p { font-size: 0.85rem; color: #57534e; line-height: 1.4; }
    .test-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
        margin-top: 14px;
    }
    .test-info-item {
        background: white;
        border-radius: 8px;
        padding: 8px;
        font-size: 0.75rem;
    }
    .test-info-label { color: #a8a29e; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.3px; }
    .test-info-value { font-weight: 600; margin-top: 2px; }

    /* ── Loading / Error ── */
    .loading { text-align: center; color: #a8a29e; padding: 20px; font-size: 0.85rem; }
</style>
</head>
<body>
<div id="app" class="loading">Chargement du widget…</div>

<script type="module">
    import { App } from 'https://cdn.jsdelivr.net/npm/@modelcontextprotocol/ext-apps@1.1.2/dist/src/app-with-deps.js';

    const container = document.getElementById('app');
    const app = new App({ name: 'GroceryWidget', version: '1.0.0' }, {});

    function parseData(params) {
        const blocks = (params.content ?? []).filter(c => c.type === 'text');
        for (const block of blocks) {
            try { return JSON.parse(block.text); } catch {}
        }
        return null;
    }

    function renderPicker(data) {
        container.innerHTML = `
            <div class="picker-header">
                <h2>Quel pain pour « ${data.listName} » ?</h2>
                <p>Choisissez le type de pain à ajouter</p>
            </div>
            <div class="picker-grid">
                ${data.suggestions.map((s, i) => `
                    <div class="picker-card" data-index="${i}">
                        <div class="picker-emoji">${s.emoji}</div>
                        <div class="picker-name">${s.name}</div>
                        <div class="picker-desc">${s.description}</div>
                        <div class="picker-price">${s.price}</div>
                    </div>
                `).join('')}
            </div>
        `;

        container.querySelectorAll('.picker-card').forEach(card => {
            card.addEventListener('click', async () => {
                const idx = parseInt(card.dataset.index);
                const selected = data.suggestions[idx];

                card.classList.add('selected');
                container.querySelectorAll('.picker-card').forEach(c => {
                    if (c !== card) c.style.opacity = '0.5';
                });

                container.insertAdjacentHTML('beforeend',
                    `<div class="picker-status">Ajout de « ${selected.name} »…</div>`
                );

                try {
                    await app.callServerTool({
                        name: 'add-item',
                        arguments: { listId: parseInt(data.listId), name: selected.name },
                    });

                    container.querySelector('.picker-status').textContent =
                        `✓ « ${selected.name} » ajouté à ${data.listName}`;
                } catch (err) {
                    container.querySelector('.picker-status').textContent =
                        `Erreur: ${err.message}`;
                }
            });
        });
    }

    function renderTest(data) {
        const info = data.serverInfo;
        container.innerHTML = `
            <div class="test-card">
                <div class="test-badge">P3 — MCP Apps</div>
                <h2>${data.title}</h2>
                <p>${data.message}</p>
                <div class="test-info">
                    ${Object.entries(info).map(([key, val]) => `
                        <div class="test-info-item">
                            <div class="test-info-label">${key}</div>
                            <div class="test-info-value">${val}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    function render(data) {
        if (!data) return;
        if (data.mode === 'picker') renderPicker(data);
        else if (data.mode === 'test') renderTest(data);
        else container.textContent = JSON.stringify(data, null, 2);
    }

    // Receive tool result from host
    app.ontoolresult = (params) => {
        const data = parseData(params);
        if (data) render(data);
    };

    // Receive tool input (arguments) — render early if data is in args
    app.ontoolinput = (params) => {
        if (params.arguments && typeof params.arguments === 'object') {
            render(params.arguments);
        }
    };

    try {
        await app.connect();
        container.textContent = 'Widget connecté, en attente de données…';
    } catch (err) {
        container.textContent = 'Erreur de connexion: ' + err.message;
    }
</script>
</body>
</html>
HTML;
    }
}
