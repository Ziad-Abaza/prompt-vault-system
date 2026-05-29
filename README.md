# Atlas Library

Atlas Library is a professional, production-ready knowledge management platform designed for storing, organizing, categorizing, and managing AI prompts and prompt collections. Built with a focus on simplicity, portability, and security, it provides a centralized workspace for teams and individuals to build their prompt intelligence.

## 🚀 Key Features

- **Prompt Management**: Full CRUD operations for AI prompts with title, content, and classification.
- **Advanced Organization**:
    - **Categories**: High-level classification (one per prompt).
    - **Granular Tags**: Many-to-many relationship for flexible cross-referencing.
    - **Collections**: Logical workspaces to group related prompts for specific workflows.
- **Smart Search**: High-performance full-text search across titles and content with integrated filters.
- **Multi-User System**: Private vaults for every user with secure Bcrypt-based authentication.
- **Data Portability**: Comprehensive JSON-based Import/Export system to backup or migrate your entire library while preserving all relationships.
- **Modern UI/UX**: Distraction-free interface built with Tailwind CSS, featuring a persistent sidebar, responsive design, and mono-spaced prompt reading areas.
- **One-Click Copy**: Integrated copy-to-clipboard functionality with visual success feedback.

## 🛡️ Security Features

Atlas Library is built with production-grade security standards:
- **Environment Isolation**: Sensitive keys and configuration managed via `.env`.
- **SQL Injection Prevention**: 100% prepared statements using PDO.
- **XSS Protection**: Automatic output escaping for all dynamic content.
- **CSRF Protection**: Cryptographically secure tokens for all state-changing requests.
- **Secure Sessions**: Hardened session cookies with HttpOnly, SameSite, and Secure flags.
- **Input Validation**: Centralized validation layer for all user-submitted data.

## 🛠️ Tech Stack

- **Backend**: PHP (Pure, no frameworks)
- **Database**: SQLite (Self-initializing, zero configuration)
- **Frontend**: HTML5, Vanilla JavaScript, Tailwind CSS (via Play CDN)
- **Typography**: Inter (UI) & JetBrains Mono (Content)

## 📦 Installation & Deployment

Atlas Library is designed to be fully portable. No complex installation or build steps are required.

1.  **Clone or Copy** the project folder to your PHP-enabled web server (e.g., Apache, Nginx, or local tools like XAMPP/MAMP).
2.  **Configuration**:
    - Rename `.env.example` to `.env`.
    - (Optional) Customize `APP_NAME`, `SECURITY_KEY`, and `CSRF_SECRET` for production use.
3.  **Permissions**: Ensure the `data/` directory is writable by the web server (it will automatically create the SQLite database on first run).
4.  **Run**: Navigate to the folder in your browser. You will be redirected to the login page to start your vault.

## 📂 Project Structure

```text
/
├── data/                 # SQLite database storage (auto-created)
├── includes/             # Core application modules
│   ├── auth.php          # Authentication logic
│   ├── db.php            # Database connection & schema
│   ├── env.php           # Environment variable loader
│   ├── models.php        # Business logic & CRUD operations
│   ├── security.php      # Validation & CSRF protection
│   ├── header.php        # UI Shell (Sidebar & Head)
│   └── footer.php        # UI Shell (Scripts & Bottom)
├── bootstrap.php         # Application initialization
├── .env                  # Local environment configuration
├── index.php             # Dashboard / Prompt Library
├── prompt.php            # Detail view
├── prompt_edit.php       # Create / Edit interface
├── categories.php        # Category management
├── tags.php              # Tag management
├── collections.php       # Workspace management
├── search.php            # Advanced search interface
├── import.php            # Data restoration
└── export.php            # Data backup
```

## 📝 License

This project is licensed under the MIT License - feel free to use and adapt it for your own needs.

---
*Built for the next generation of prompt engineering.*
