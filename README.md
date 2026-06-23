# 🍳 FlatBite CMS (God Mode V6.0)

**FlatBite CMS** is a lightning-fast, zero-database, flat-file content management system purpose-built for recipe websites and food blogs. It uses pure PHP and JSON to bypass the bloat of traditional MySQL-based systems like WordPress, resulting in instant load times, zero database maintenance, and effortless deployment.

---

## 🚀 Core Features

* **Zero-Database Architecture:** All data, settings, and users are stored securely in flat `.json` files. 
* **Auto WebP Image Compression:** Upload heavy MB images; the engine automatically resizes, optimizes, and converts them to next-gen lightweight `.webp` formats.
* **SEO & Google Rich Snippets:** Automatically translates your recipes into Google's `JSON-LD` Schema so your dishes rank with photos, ratings, and prep times in search results.
* **Ironclad Routing:** Unpublished drafts are aggressively hidden from the public frontend and return a strict 404 error to unauthorized visitors.
* **God Mode Dashboard:** A premium, unified UI built with Tailwind CSS for managing your kitchen, operatives, and system settings.
* **1-Click ZIP Backups:** Export your entire site (recipes, images, and settings) directly from the admin dashboard.

---

## 📂 Directory Structure

Before installing, ensure your repository matches this exact structure. Note the `.gitkeep` files—these ensure your empty directories are tracked by Git without committing personal data.

```text
flatbite-cms/
│
├── index.php                 # Frontend Engine & Routing
├── README.md                 # Documentation
│
├── admin/
│   └── index.php             # God Mode Backend & Setup Wizard
│
├── data/                     # Secure Data Vault
│   ├── .gitkeep              
│   ├── settings.json         # (Auto-generated during setup)
│   ├── users.json            # (Auto-generated during setup)
│   ├── .htaccess             # (Auto-generated to block public access)
│   └── recipes/
│       └── .gitkeep          # Auto-populated with your dish JSON files
│
└── uploads/                  # Media Vault
    └── .gitkeep              # Auto-populated with compressed WebP images