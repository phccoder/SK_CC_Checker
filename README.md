# SK & CC Checker - A PHP-Based Stripe Key Tool

![PHC Coder Logo](assets/img/phccoder.jpg)

A web-based tool designed for checking the validity of Stripe Secret Keys (SK) and credit card information against multiple Stripe APIs. This project was developed as a personal exercise and is intended for **educational purposes only**.

---

## Features ✨

* **Multiple Stripe APIs**: Test against four different Stripe API configurations with varying risk levels.
* **Authentication**: Secure your checker with a simple, hash-based password system.
* **CC Generator**: Includes a built-in credit card generator for creating test numbers based on a BIN.
* **Telegram Integration**: Get instant notifications for valid (CVV/CCN) results forwarded to your Telegram chat via a bot.
* **Glassmorphism UI**: A fully responsive, mobile-friendly design with frosted-glass cards, animated gradient background, and custom glass toasts.
* **No Page-Reload Theme Toggle**: Switch between dark (default) and light modes instantly with a floating toggle button — persisted in `localStorage`.
* **Live Result Filtering**: Tap the CVV / CCN / DEAD counter pills to show or hide each result category; per-category clear buttons included.
* **No XAMPP Required**: Runs on PHP's built-in web server via a one-click `serve.ps1` launcher.

---

## 🚀 Setup and Installation

No XAMPP required — this project runs on **PHP's built-in web server** (PHP itself is still needed; a standalone lightweight PHP install works fine).

1.  **Install PHP (standalone, no XAMPP)**:
    Download the latest Windows thread-safe ZIP from [windows.php.net](https://windows.php.net/download/), extract it, and add the folder containing `php.exe` to your system PATH (or set `$phpPath` in `serve.ps1`).
    > Make sure the `curl` and `openssl` extensions are enabled in your `php.ini` (the built-in server needs them to reach the Stripe API).
2.  **Start the app** (one click):
    ```powershell
    .\serve.ps1
    ```
    This launches the built-in web server at `http://localhost:8080`.
3.  **Access the Checker**: Open your browser and navigate to `http://localhost:8080`.
    (Change the port with `.\serve.ps1 -Port 9090` if needed.)

> Prefer XAMPP/WAMP? Just drop the folder into `htdocs` and open `http://localhost/SK_CC_Checker/` — no changes needed.

---

## 💻 Using the Checker

1.  **Cards input**: Paste your card list into the textarea, one card per line in `NUMBER|MM|YYYY|CVV` format.
2.  **API**: Pick one of the four Stripe API configurations from the dropdown. APIs marked `[need SK]` reveal the **Secret Key (SK)** field.
3.  **Secret Key**: Paste your `sk_live_...` key. Use the copy button to copy it to your clipboard, or **CHECK SK** to validate the key itself.
4.  **CHECK CARDS**: Starts checking each card line with a configurable delay. Live cards pop a glass toast; counters (CVV / CCN / DEAD) update live in the stats bar.
5.  **CC Gen**: Opens the generator modal — enter a BIN, choose month/year/quantity, and generate valid-format test numbers.
6.  **Settings (gear icon)**: Configure the Telegram forwarder (Chat ID + message type) and the per-card check delay.
7.  **Theme**: Use the floating ☀/☾ button to toggle dark and light glass themes instantly.

---

## ⚙️ Configuration

All the main settings are located in the **`config.php`** file.

### Authentication

To enable a password prompt for your checker, follow these steps:

1.  **Enable Auth**: In `config.php`, change `$forceAuth` to `true`.
    ```php
    $forceAuth = true; // Set to true to require a password
    ```
2.  **Set Your Password**:
    * Create a temporary PHP file (e.g., `hash_gen.php`) with the following code:
        ```php
        <?php
        echo password_hash('YourNewSecurePassword', PASSWORD_DEFAULT);
        ?>
        ```
    * Run it and copy the resulting hash.
    * Paste the hash into the `$AuthPass_Hashed` variable in `config.php`:
        ```php
        $AuthPass_Hashed = '$2y$10$....your....long....pasted....hash....';
        ```

### Telegram Bot

To receive live results on Telegram:

1.  **Get Your Bot Token**: Talk to [@BotFather](https://t.me/BotFather) on Telegram to create a new bot and get its API token.
2.  **Add Token to Config**: Paste the token into the `$bot_token` variable in `config.php`.
    ```php
    $bot_token = "6188077264:AAFaIbSeHyokvnXQ84eVtnJCGWWBPHHNd2c"; // Replace with your bot's token
    ```
3.  **Get Your Chat ID**: Start a chat with your new bot and then visit `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates` (replace `<YOUR_BOT_TOKEN>` with your actual token). Find your chat ID in the response.
4.  **Use in Checker**: Enter this Chat ID in the "Settings" modal of the checker to start receiving notifications.

---

## 🗂️ Project Structure

| File | Purpose |
|------|---------|
| `index.php` | Main checker interface (glassmorphism UI) |
| `auth.php` | Login page when `$forceAuth` is enabled |
| `config.php` | All settings (auth, Telegram, API list, bug bins) |
| `init.php` | Session / HTTPS bootstrap |
| `preset.php` | Shared request parsing + validation for the Stripe API files |
| `skcheck.php` | Standalone SK validity checker |
| `Stripe_1..4_New.php` | The four Stripe API checking flows |
| `telebot.php` | Telegram forwarder test endpoint |
| `serve.ps1` | One-click PHP built-in server launcher (no XAMPP) |
| `assets/css/style.css` | All glassmorphism styling + theme variables |
| `assets/js/main.js` | Checker logic, CC generator, toasts, theme toggle |

---

## 💖 Support Me

If you find this project helpful or have learned something from the source code, please consider supporting me. It helps me to create more open-source projects.

[![Ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/phcc0d3r)

## 📞 Contact & Links

* **Telegram Bot**: Get updates and test the forwarder with [@phc_cc_chcker_bot](t.me/phc_cc_chcker_bot)
* **Developer Contact**: Reach out to [phccoder on Telegram](t.me/PHCC0D3r)

---

## ⚠️ Disclaimer

This tool is provided for educational and testing purposes ONLY. The author is not responsible for any misuse of this application. Using this tool on credit card information that you do not have explicit permission to use is illegal and unethical.