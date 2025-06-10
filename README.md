# GH-Timeline – GitHub Timeline Email Subscription System

A pure PHP-based email verification system that allows users to subscribe to GitHub timeline updates via email. Users register with email, confirm using a verification code, and receive updates every 5 minutes via a CRON job. Includes a secure unsubscribe system with confirmation codes.

---

## 📽️ Demo Video

👉 [Click here to watch the demo](https://drive.google.com/file/d/1f4k7Wd2Bkk-jRCM6tzxABdO-dfu4WZY8/view?usp=drive_link)  

---

## 🚀 Features

- ✅ Email registration with 6-digit code verification
- ✅ GitHub timeline updates sent via HTML email every 5 minutes
- ✅ Secure unsubscribe with confirmation
- ✅ Stores emails in `registered_emails.txt` (no DB used)
- ✅ Pure PHP — no frameworks or libraries
- ✅ Compatible with both Windows (Task Scheduler) and Linux (CRON)

---

## ✅ Requirements
- PHP 8.0 or higher
- Enabled openssl extension
- cacert.pem configured if using HTTPS endpoints

## 🤝 Author
Vansh Kothari
Email: vanshkothari07@gmail.com
GitHub: https://github.com/VanshK07
