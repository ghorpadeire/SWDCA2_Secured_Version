# 🔐 SecureWebApp-NodeJS

A security-enhanced version of a basic web application developed as part of the Secure Web Development module at the National College of Ireland (NCI).

This project demonstrates how to take a standard web application and apply real-world security features using industry best practices, aligning with OWASP Top 10 standards.

---

## 📚 Project Context

This project was completed as part of **Secure Web Development (SWDCA2)** coursework at **National College of Ireland**.  
The objective was to understand and implement essential security features on a simple web application.

---

## 🚀 Features Implemented

- 🔑 User Authentication – Secure login system with hashed passwords and session control  
- 🔒 Role-Based Authorization – Admin/user access control for protected routes  
- 🧼 Input Validation & Sanitization – Prevents XSS, SQL injection, and other injection attacks  
- 🌐 HTTPS Support – Secure communication over SSL/TLS (in production)  
- 📦 Environment Configuration – Uses `.env` for sensitive data management  
- 🛡️ Secure Headers – Implemented headers like CSP, HSTS, X-Frame-Options  
- 🧠 Session Management – HttpOnly and Secure cookie settings with session expiry  
- 📋 Audit Logging – Logs login, logout, and critical user actions  
- 🚫 Rate Limiting – Prevents brute-force and denial-of-service attempts

---

## 🛠️ Technologies Used

- **Backend:** Node.js (Express)  
- **Frontend:** HTML/CSS, Bootstrap  
- **Database:** MongoDB  
- **Security:** bcrypt, helmet, express-session, express-validator  
- **Environment Handling:** dotenv  
- **Containerization (Optional):** Docker, Docker Compose

---

## 📁 Folder Structure

```
SecureWebApp-NodeJS/
├── public/             # Static files (HTML/CSS)
├── routes/             # Express route files
├── middleware/         # Authentication, logging, validation
├── models/             # MongoDB models
├── views/              # Templating files (if used)
├── .env                # Environment secrets (not tracked)
├── app.js              # Main app entry
├── Dockerfile          # Docker setup
└── README.md
```

---

## 📦 Installation & Setup

### Prerequisites

- Node.js v18+
- MongoDB installed or MongoDB Atlas URI
- (Optional) Docker & Docker Compose

### Step-by-Step

```bash
# 1. Clone the repository
git clone https://github.com/ghorpadeire/SWDCA2_Secured_Version.git
cd SWDCA2_Secured_Version

# 2. Install dependencies
npm install

# 3. Create a .env file
touch .env
```

Sample `.env` file:

```env
PORT=3000
MONGO_URI=mongodb://localhost:27017/swdca2_secure
SESSION_SECRET=your_secret_key
```

```bash
# 4. Start the application
npm start
```

Open in browser: `http://localhost:3000`

---

## 🐳 Docker Usage (Optional)

```bash
docker build -t swdca2-secure .
docker run -p 3000:3000 swdca2-secure
```

---

## 🔎 Security Demonstrations

| Feature              | Description                                                  |
|----------------------|--------------------------------------------------------------|
| Password Hashing     | bcrypt used to hash and store user passwords securely        |
| Session Hardening    | Sessions secured via HttpOnly, Secure cookies, and expiry    |
| Role-based Access    | Only admins can access protected admin routes                |
| Input Validation     | express-validator to sanitize and validate all inputs        |
| HTTPS Setup          | Configurable for secure HTTPS communication                  |
| Rate Limiting        | express-rate-limit protects endpoints from abuse             |
| Security Headers     | helmet sets common protection headers                        |

---

## 🎓 Academic Contribution

This project helped reinforce the following cybersecurity principles:

- Defense in depth  
- Least privilege  
- Secure coding practices  
- Session & token-based authentication  
- Secure deployment strategies  

---

## 👨‍💻 Author

**Pranav Ghorpade**  
MSc Cybersecurity | National College of Ireland  
GitHub: [ghorpadeire](https://github.com/ghorpadeire)  
Email: pranav.ghorpade3108@gmail.com

---

## 📄 License

This project is licensed for academic and demonstration use.  
MIT License (can be updated as needed).
