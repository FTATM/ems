# วิธีใช้ Git เบื้องต้น

## 1. ตั้งค่า Git ครั้งแรก

```sh
git config --global user.name "ชื่อของคุณ"
git config --global user.email "อีเมลของคุณ"
```

## 2. สร้าง Repository ใหม่

```sh
git init
```

## 3. เชื่อมต่อกับ Repository ที่มีอยู่แล้ว

```sh
git clone <url>
```

## 4. ตรวจสอบสถานะไฟล์

```sh
git status
```

## 5. เพิ่มไฟล์เข้าสู่ staging area

```sh
git add <ชื่อไฟล์>
# หรือเพิ่มทั้งหมด
git add .
```

## 6. บันทึกการเปลี่ยนแปลง (commit)

```sh
git commit -m "ข้อความอธิบายการเปลี่ยนแปลง"
```

## 7. ดูประวัติการ commit

```sh
git log
```

## 8. สลับไปยัง branch อื่น

```sh
git checkout <branch>
```
- ใช้สำหรับเปลี่ยนไปทำงานที่ branch อื่น

## 9. สร้าง branch ใหม่

```sh
git branch <new branch>
```
- ใช้สำหรับสร้าง branch ใหม่ (แต่ยังไม่ได้สลับไป branch นั้น)

## 10. ดู branch ทั้งหมด

```sh
git branch
```

## 11. ส่งโค้ดขึ้น remote repository

```sh
git push
```

## 12. ดึงโค้ดจาก remote repository

```sh
git pull
```

## 13. ดึงโค้ดจาก branch master ของ remote

```sh
git pull origin master
```
- ใช้สำหรับดึงโค้ดจาก branch `master` ของ remote repository (origin) มารวมกับ branch ปัจจุบัน

---

**Tip:**  
- ใช้ `git help` เพื่อตรวจสอบคำสั่ง Git อื่นๆ ที่คุณสามารถใช้ได้