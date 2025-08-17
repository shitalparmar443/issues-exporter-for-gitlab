# Issues Exporter for GitLab

**Contributors:** [shitalparmar443](https://github.com/shitalparmar443)  
**Donate link:** [https://paypal.me/shitalparmar443](https://paypal.me/shitalparmar443)  
**Tags:** gitlab, csv export, issues, ajax, fluent boards  
**Requires at least:** 5.8  
**Tested up to:** 6.8  
**Stable tag:** 1.0  
**Requires PHP:** 7.4  
**License:** GPLv2 or later  
**License URI:** [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)  

Export GitLab issues to a CSV file with AJAX progress tracking and Fluent Boards compatibility.

---

## 📖 Description

This WordPress plugin allows you to export **GitLab issues** to a CSV file.  
Features include:  
- **AJAX-based progress tracking** during export.  
- **Fluent Boards compatibility** for streamlined workflows.  
- Ability to **manage and delete old CSV files** from the admin panel.  

---

## 🚀 Installation

### From WordPress Admin
1. Go to **Plugins → Add New**.  
2. Search for **Issues Exporter for GitLab**.  
3. Click **Install** and then **Activate**.  

### Manual Installation
1. Upload the plugin folder `issues-exporter-for-gitlab` to the `/wp-content/plugins/` directory.  
2. Activate the plugin via the **Plugins** menu in WordPress.  

---

## 🔑 How to Create a Project Access Token in GitLab

### Prerequisites
- You must have sufficient permissions — typically **Owner** or **Maintainer**.  
- On **GitLab.com**, Project Access Tokens require a **Premium or Ultimate subscription**, or an active trial.  
- On **self-managed GitLab instances**, tokens are generally available without subscription restrictions.  

📄 References:  
- [GitLab Docs](https://docs.gitlab.com/user/project/settings/project_access_tokens/)  
- [GitLab Forum](https://forum.gitlab.com/)  

### Step-by-Step Guide
1. **Log in** to GitLab and navigate to your project.  
2. In the left sidebar, go to **Settings → Access Tokens**.  
3. Click **Add new token**.  
4. Fill out token details:  
   - **Name**: A descriptive identifier.  
   - *(Optional)* **Description** (available since GitLab 17.7).  
   - **Expiration date**: Defaults to 30 days. Non-expiring tokens were removed in GitLab 16.0.  
   - **Role**: Choose the appropriate role (Guest, Reporter, Developer, Maintainer, Owner).  
   - **Scopes**: Select the required scopes, such as `api` or `read_api`.  
5. Click **Create project access token**.  
6. **Copy the token immediately** — it will only be shown once.  

---

## 🌐 External Services

This plugin connects to the **GitLab API** to retrieve project issues and export them as CSV files.

- **Data sent**: Project ID & Access token (for authentication).  
- **Data usage**: Used only for fetching issues during export.  
- **No personal WordPress user data** is shared with GitLab.  

**Service Provider:** GitLab Inc.  
- [Terms of Service](https://about.gitlab.com/terms/)  
- [Privacy Policy](https://about.gitlab.com/privacy/)  

---

## ❓ Frequently Asked Questions

**Q: How many issues can it export?**  
A: Up to **5000 issues**, processed in batches of 50.  

**Q: Is my access token saved securely?**  
A: Yes, it is stored using the WordPress Options API and never exposed in frontend HTML.  

**Q: Can I delete old CSV files?**  
A: Yes, all generated files are listed in the admin page with delete buttons.  

---

## 🖼 Screenshots

1. Issues Exporter for GitLab **Settings screen**  
2. Export progress and **status display**  
3. **List of generated CSV files**  

---

## 📌 Changelog

### 1.0
- Initial release with issue export, live progress tracking, and file cleanup.  

---

## ⬆️ Upgrade Notice

### 1.0
First stable release of **Issues Exporter for GitLab**.  
