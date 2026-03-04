Here is a **clean and professional `README.md`** for your **Daily Streak App Laravel project**. You can copy this directly into your repository.

---

```markdown
# 🔥 Daily Streak Management App

A simple **Laravel-based daily streak tracker** that helps track consistency like GitHub contributions.  
Users can mark their daily progress and maintain a streak with a clean and modern UI.

The project includes **streak tracking, freeze days, and a GitHub-style contribution feel**.

---

## ✨ Features

- 📅 Track daily streaks
- ❄️ Freeze system to prevent streak loss
- 🎉 Confetti animation when completing a contribution
- ⚡ Fast UI using TailwindCSS
- 📊 Contribution-style visual indicator
- 🔐 CSRF protected API request
- 💡 Minimal and modern interface

---

## 🖼 Preview

The app displays:

- Current streak count
- Streak status (Completed / Pending)
- Freeze balance
- Contribution deployment button
- Visual contribution squares

---

## 🛠 Tech Stack

- **Laravel**
- **Blade Templates**
- **TailwindCSS**
- **JavaScript (Fetch API)**
- **Canvas Confetti**

---

## 📂 Project Structure

```

daily-streak-app
│
├── app
├── bootstrap
├── config
├── database
├── public
├── resources
│   └── views
│       └── streak.blade.php
├── routes
│   └── web.php
├── storage
└── vendor

````

---

## ⚙️ Installation

### 1️⃣ Clone the repository

```bash
git clone https://github.com/KhushaliSavaliya/daily-streak-app.git
````

### 2️⃣ Go to project folder

```bash
cd daily-streak-app
```

### 3️⃣ Install dependencies

```bash
composer install
```

### 4️⃣ Copy environment file

```bash
cp .env.example .env
```

### 5️⃣ Generate app key

```bash
php artisan key:generate
```

### 6️⃣ Setup database

Update `.env` with your database credentials.

Then run:

```bash
php artisan migrate
```

### 7️⃣ Start development server

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

## 🚀 How It Works

1. The user opens the dashboard.
2. Current streak and status are displayed.
3. User clicks **Deploy Contribution**.
4. A request is sent to:

```
POST /streak/update
```

5. The backend updates the streak count.
6. UI updates with animation and confetti.

---

## 📸 UI Features

* Dark mode design
* TailwindCSS styling
* Contribution progress indicators
* Interactive animations

---

## 📌 Future Improvements

* User authentication
* Multiple users
* Streak history
* Calendar view
* Mobile responsive improvements
* GitHub style contribution graph

---

## 👩‍💻 Author

**Khushali Savaliya**

GitHub
[https://github.com/KhushaliSavaliya](https://github.com/KhushaliSavaliya)

---

## 📄 License

This project is open-source and available under the **MIT License**.

```

---
