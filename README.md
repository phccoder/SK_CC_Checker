# SK & CC Checker - A PHP-Based Stripe Key Tool

![PHC Coder Logo](assets/img/phccoder.jpg)

A web-based tool designed for checking the validity of Stripe Secret Keys (SK) and credit card information against multiple Stripe APIs. This project was developed as a personal exercise and is intended for **educational purposes only**.

---

## Features ✨

* **Multiple Stripe APIs**: Test against four different Stripe API configurations with varying risk levels.
* **Authentication**: Secure your checker with a simple, hash-based password system.
* **CC Generator**: Includes a built-in credit card generator for creating test numbers based on a BIN.
* **Telegram Integration**: Get instant notifications for valid (CVV/CCN) results forwarded to your Telegram chat via a bot.
* **Theming**: Switch between a sleek dark mode and a clean light mode to suit your preference.
* **Modern UI**: A clean, responsive interface built with Bootstrap 5.

---

## 🚀 Setup and Installation

To run this checker on your local machine, you will need a web server environment like XAMPP.

1.  **Install XAMPP**: Download and install XAMPP from the [official website](https://www.apachefriends.org/index.html).
2.  **Move Project Files**: Place the entire `SK_CC_Checker` folder inside the `htdocs` directory (usually located at `C:\xampp\htdocs\`).
3.  **Start Apache**: Open the XAMPP Control Panel and start the **Apache** service.
4.  **Access the Checker**: Open your web browser and navigate to `http://localhost/SK_CC_Checker/`.

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

## 💖 Support Me

If you find this project helpful or have learned something from the source code, please consider supporting me. It helps me to create more open-source projects.

[![Ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/phcc0d3r)

## 📞 Contact & Links

* **Telegram Bot**: Get updates and test the forwarder with [@phc_cc_chcker_bot](t.me/phc_cc_chcker_bot)
* **Developer Contact**: Reach out to [phccoder on Telegram](t.me/PHCC0D3r)

---

## ⚠️ Disclaimer

This tool is provided for educational and testing purposes ONLY. The author is not responsible for any misuse of this application. Using this tool on credit card information that you do not have explicit permission to use is illegal and unethical.