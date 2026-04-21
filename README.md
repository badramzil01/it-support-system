# 🤖 Intelligent IT Support Automation System

## 📌 Project Overview

This project is a Final Year Project (PFE) that aims to automate IT support using chatbot logic, artificial intelligence, and workflow automation.

Users can send IT problems through a chat interface, and the system will automatically analyze and provide solutions.

---

## 🎯 Objectives

* Automate IT support processes
* Reduce manual workload
* Provide fast and intelligent responses
* Build a real-world enterprise system

---

## 🏗️ System Architecture

User → Bot (Azure) → n8n → Laravel API → Database / AI → Jira

---

## ⚙️ Technologies Used

* **Backend**: Laravel (PHP)
* **Database**: MySQL
* **Automation**: n8n
* **AI**: OpenAI API
* **Bot**: Azure Bot Service
* **Ticketing**: Jira API
* **Frontend**: Bootstrap / Vue.js

---

## 🧠 Core Features

* 🔍 Search in Knowledge Base
* 🤖 AI-generated solutions (OpenAI)
* 🎫 Automatic ticket creation (Jira)
* 💬 Chatbot interaction
* 📊 Admin dashboard (statistics & monitoring)

---

## 🗄️ Database Structure

Main tables:

* users
* messages
* tickets
* knowledge_base
* notifications
* categories

---

## 🔄 Workflow

1. User sends a message
2. Bot sends request to Laravel API
3. System checks knowledge base
4. If found → return solution
5. If not found → generate solution using AI
6. Create ticket in Jira
7. Send response back to user
8. Save new solution in database

---

## 🚀 Installation

```bash
git clone https://github.com/badramzil01/it-support-system.git
cd it-support-system
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

## 🧪 Testing

```bash
php artisan tinker
```

---

## 👥 Team

* Badr Amzil
* (Add your team members)

---

## 📌 Future Improvements

* Multi-language support
* Advanced AI classification
* Real-time notifications
* SLA management
* Performance analytics

---

## 📄 License

This project is for educational purposes (PFE).

---
