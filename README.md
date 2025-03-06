# Exam Management System

## Overview
This is a web-based exam management system developed using PHP and MySQL. The system allows administrators to manage users, create and manage exams, and track results efficiently.

## Features
- User authentication (Login/Logout)
- Admin panel for managing users and exams
- Exam creation and modification
- User-friendly interface

## Installation
1. Clone the repository:
   ```sh
   git clone https://github.com/Tawanchai2547/Exam.git
   ```
2. Start XAMPP and ensure Apache & MySQL are running.
3. Import the database:
   - Open `phpMyAdmin` and create a database (e.g., `exam_db`).
   - Import the SQL file provided in the project.
4. Configure the database connection:
   - Open `config.php` and set the correct database credentials.
5. Run the project in the browser:
   ```
   http://localhost/Exam
   ```

## Security Improvements Needed
- Use `password_hash()` and `password_verify()` for secure password storage.
- Implement session management to prevent unauthorized access.
- Protect against SQL injection by using prepared statements.

## Contribution
If you want to contribute, feel free to fork the repository and submit a pull request.

## License
This project is open-source and available under the MIT License.





ระบบจัดการข้อสอบ (Exam Management System)

ภาพรวม

นี่คือระบบจัดการข้อสอบบนเว็บที่พัฒนาด้วย PHP และ MySQL ระบบนี้ช่วยให้ผู้ดูแลสามารถจัดการผู้ใช้ สร้างและแก้ไขข้อสอบ และติดตามผลการสอบได้อย่างมีประสิทธิภาพ

คุณสมบัติของระบบ

ระบบยืนยันตัวตนของผู้ใช้ (เข้าสู่ระบบ/ออกจากระบบ)

แผงควบคุมสำหรับผู้ดูแลเพื่อจัดการผู้ใช้และข้อสอบ

สร้างและแก้ไขข้อสอบได้

อินเทอร์เฟซที่ใช้งานง่าย

วิธีติดตั้ง

คัดลอกโปรเจกต์โดยใช้คำสั่ง:

git clone https://github.com/Tawanchai2547/Exam.git

เริ่มต้น XAMPP และตรวจสอบให้แน่ใจว่า Apache และ MySQL ทำงานอยู่

นำเข้าฐานข้อมูล:

เปิด phpMyAdmin และสร้างฐานข้อมูลใหม่ (เช่น exam_db)

นำเข้าไฟล์ SQL ที่ให้มาในโปรเจกต์

ตั้งค่าการเชื่อมต่อฐานข้อมูล:

เปิดไฟล์ config.php และแก้ไขข้อมูลรับรองฐานข้อมูลให้ถูกต้อง

เปิดโปรเจกต์ในเบราว์เซอร์โดยใช้ URL:

http://localhost/Exam

ข้อควรปรับปรุงด้านความปลอดภัย

ใช้ password_hash() และ password_verify() เพื่อเก็บรหัสผ่านอย่างปลอดภัย

ใช้ระบบจัดการเซสชันเพื่อป้องกันการเข้าถึงโดยไม่ได้รับอนุญาต

ป้องกัน SQL Injection โดยใช้ Prepared Statements

การมีส่วนร่วม

หากคุณต้องการร่วมพัฒนา สามารถ Fork Repository และส่ง Pull Request ได้

ใบอนุญาต

โปรเจกต์นี้เป็นโอเพ่นซอร์สและอยู่ภายใต้ MIT License

