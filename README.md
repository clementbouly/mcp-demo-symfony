# 🛒 MCP Demo Symfony — Liste de courses

Démo minimaliste d'une liste de courses en **Symfony PHP**, illustrant 4 paradigmes d'interface utilisateur pour les LLM.

> Présenté à l'**AFUP Montpellier** — 11 mars 2026

## Les 4 paradigmes

| # | Paradigme | Description | Stack |
|---|-----------|-------------|-------|
| 🔵 | **Interface classique** | L'utilisateur pilote l'interface | Twig + JS vanilla |
| 🟠 | **MCP Server** | L'IA appelle des tools structurés | `symfony/mcp-bundle` + `#[McpTool]` |
| 🟣 | **MCP Apps** | Widget interactif dans le chat | Mêmes tools MCP, rendu par le host |
| 🟢 | **WebMCP** | L'IA agit dans l'interface du site | `navigator.modelContext.registerTool()` |

## Prérequis

- PHP 8.2+
- Composer
- Symfony CLI (optionnel mais recommandé)

## Installation

```bash
git clone https://github.com/clementbouly/mcp-demo-symfony.git
cd mcp-demo-symfony
composer install
```

## Setup base de données (SQLite)

```bash
# Créer la base et le schéma
touch var/data.db
php bin/console doctrine:schema:create

# Remplir avec les données de démo
php bin/console app:seed-grocery
```

## Lancer le serveur

```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

Ouvrir [http://localhost:8000](http://localhost:8000) → **P1 Interface classique**

## Tester les paradigmes

### P1 — Interface classique
Ouvrir [localhost:8000](http://localhost:8000) dans le navigateur. L'UI Twig + JS est fonctionnelle.

### P2 — MCP Server (Claude Desktop / Claude Code)

Ajouter dans `~/Library/Application Support/Claude/claude_desktop_config.json` :

```json
{
  "mcpServers": {
    "grocery-symfony": {
      "command": "php",
      "args": ["/chemin/vers/mcp-demo-symfony/bin/console", "mcp:server"]
    }
  }
}
```

Pour **Claude Code**, créer un fichier `.mcp.json` :

```json
{
  "mcpServers": {
    "grocery-symfony": {
      "command": "php",
      "args": ["bin/console", "mcp:server"]
    }
  }
}
```

Relancer Claude → les tools `get-lists`, `add-item`, `toggle-item`... sont disponibles.

### P3 — MCP Apps
Même serveur MCP que P2. Le host (Claude) gère le rendu du widget dans le chat.

### P4 — WebMCP (Chrome Canary + MCP-B)

1. Lancer le serveur Symfony (`symfony server:start`)
2. Ouvrir **Chrome Canary** avec l'extension [MCP-B](https://docs.mcp-b.ai/)
3. Naviguer vers [localhost:8000](http://localhost:8000)
4. Les tools WebMCP apparaissent dans l'extension (déclarés via `navigator.modelContext.registerTool()`)

## Structure du projet

```
src/
├── Controller/
│   ├── GroceryController.php  # P1: Route / → template Twig
│   └── ApiController.php      # API REST JSON
├── Entity/
│   ├── GroceryList.php        # id, name, position
│   └── GroceryItem.php        # id, name, checked, list
├── Mcp/
│   └── GroceryTools.php       # P2: #[McpTool] (9 tools)
├── Command/
│   └── SeedGroceryCommand.php # Données de démo
└── Repository/
    ├── GroceryListRepository.php
    └── GroceryItemRepository.php

templates/grocery/index.html.twig  # P1 + P4 (WebMCP JS)
config/packages/mcp.yaml          # Config MCP Server
```

## API REST

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/lists` | Toutes les listes avec items |
| POST | `/api/lists` | Créer une liste |
| PUT | `/api/lists/{id}` | Renommer une liste |
| DELETE | `/api/lists/{id}` | Supprimer une liste |
| POST | `/api/lists/{id}/items` | Ajouter un item |
| DELETE | `/api/items/{id}` | Supprimer un item |
| PATCH | `/api/items/{id}/toggle` | Cocher/décocher |
| PUT | `/api/items/{id}` | Renommer un item |
| POST | `/api/items/{id}/move` | Déplacer un item |

## MCP Tools

| Tool | Params | Description |
|------|--------|-------------|
| `get-lists` | — | Retourne toutes les listes |
| `create-list` | `name` | Crée une liste |
| `delete-list` | `listId` | Supprime une liste |
| `rename-list` | `listId`, `name` | Renomme une liste |
| `add-item` | `listId`, `name` | Ajoute un item |
| `remove-item` | `itemId` | Supprime un item |
| `toggle-item` | `itemId` | Coche/décoche |
| `edit-item` | `itemId`, `name` | Renomme un item |
| `move-item` | `itemId`, `targetListId` | Déplace un item |

## Pour aller plus loin

- **WebMCP Bundle** : [`yoanbernabeu/webmcp-bundle`](https://packagist.org/packages/yoanbernabeu/webmcp-bundle) — déclare les tools WebMCP avec des attributs PHP `#[AsWebMcpTool]`
- **Article complet** : [L'interface utilisateur à l'heure des LLM](https://clementbouly.com/blog/mcp-ui-paradigms)

## Licence

MIT
