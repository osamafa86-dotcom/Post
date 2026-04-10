# دليل النشر التلقائي — GitHub → cPanel (ppost)

هذا الدليل يربط مشروع Laravel على cPanel بمستودع GitHub `osamafa86-dotcom/post`
بحيث يصبح أي دفع (`git push`) إلى `main` قابلاً للنشر بضغطة زر واحدة من cPanel.

---

## نظرة عامة على البنية

```
GitHub (main)  ──►  cPanel "Update from Remote"  ──►  Deploy HEAD  ──►  .cpanel.yml يُنفَّذ
                                                                      ├─ مسح الكاش
                                                                      ├─ إعادة بناء caches
                                                                      └─ تصاريح + روابط
```

- **مسار المشروع على cPanel:** `/home/z4sww4p4xieh/public_html/ppost.emdatra.org/`
- **المستودع:** `osamafa86-dotcom/post`
- **الفرع المنشور:** `main`

---

## المرحلة الأولى — رفع المشروع الكامل أول مرّة (يُنفّذ مرة واحدة)

### الخطوة 1: أنشئ Personal Access Token من GitHub
1. اذهب إلى: `https://github.com/settings/tokens?type=beta`
2. **Generate new token** → Fine-grained
3. **Repository access:** Only select → `osamafa86-dotcom/post`
4. **Permissions:** Contents = Read & Write
5. انسخ التوكن (يبدأ بـ `github_pat_...`)

### الخطوة 2: في Terminal cPanel، شغّل هذا السكربت كاملاً

> استبدل `GHTOKEN` بالتوكن الذي نسخته للتو.

```bash
cd ~/public_html/ppost.emdatra.org

# 1. نسخ احتياطي للأمان
cp .env ~/env-backup-$(date +%s)
echo "✓ تم النسخ الاحتياطي لـ .env"

# 2. تهيئة git إذا لم يكن مهيّأً
if [ ! -d .git ]; then
    git init -b main
    echo "✓ تم تهيئة git"
fi

# 3. إعدادات git محلية
git config user.email "deploy@ppost.local"
git config user.name "ppost cPanel"

# 4. ربط المستودع
git remote remove origin 2>/dev/null
git remote add origin https://GHTOKEN@github.com/osamafa86-dotcom/post.git
echo "✓ تم ربط GitHub"

# 5. جلب ما هو موجود حالياً على GitHub (ملفات الشريط + إعدادات النشر)
git fetch origin main

# 6. إعادة تعيين الـ index ليطابق GitHub بدون لمس ملفاتنا
git reset origin/main

# 7. سحب ملفات إعدادات النشر فقط من GitHub
git checkout origin/main -- .gitignore .cpanel.yml DEPLOY.md INSTALL_BREAKING_NEWS.md
git checkout origin/main -- resources/views/components/layouts/main/palestine_post/breaking-news.blade.php
echo "✓ تم استرجاع ملفات النشر والشريط المُحدَّث"

# 8. إضافة كل ملفات Laravel (مع احترام .gitignore)
git add -A

# 9. عرض ما سيتم رفعه (راجع قبل المتابعة)
echo ""
echo "════════════════════════════════════════════"
echo "  الملفات التي ستُرفع (إجمالي):"
echo "════════════════════════════════════════════"
git status --short | wc -l
echo ""
echo "  أمثلة:"
git status --short | head -20
echo ""

# 10. تأكيد قبل الرفع
read -p "هل تريد المتابعة والرفع؟ (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "✗ تم الإلغاء."
    exit 1
fi

# 11. Commit + Push
git commit -m "chore: initial full Laravel project import + deploy config"
git push -u origin main

echo ""
echo "════════════════════════════════════════════"
echo "  ✓ تم رفع المشروع إلى GitHub بنجاح!"
echo "════════════════════════════════════════════"
```

### الخطوة 3: تحقّق أن الـ .env ليس على GitHub
```bash
curl -s "https://api.github.com/repos/osamafa86-dotcom/post/contents/.env" | grep -q "Not Found" && echo "✓ .env آمن (غير موجود على GitHub)" || echo "✗ تحذير: .env قد يكون مرفوعاً!"
```

---

## المرحلة الثانية — ربط cPanel Git Version Control بالمستودع

> هذا يجعل cPanel يتعرّف على المجلّد كـ git repo قابل للتحديث من GitHub.

1. ادخل **cPanel → Git Version Control**.
2. اضغط **Create**.
3. املأ:
   - **Clone URL:** `https://github.com/osamafa86-dotcom/post.git`
   - **Repository Path:** `/home/z4sww4p4xieh/public_html/ppost.emdatra.org`
   - **Repository Name:** `ppost`
4. اضغط **Create**.

> ⚠️ إذا أعطى cPanel خطأ "directory not empty"، فهذا طبيعي لأنه مهيّأ
> أصلاً من السكربت السابق. اضغط **Manage** بدلاً من Create.

---

## المرحلة الثالثة — نشر التحديثات (Workflow اليومي)

### من جهازك (المطوّر):
```bash
# اعمل تعديلاتك محلياً
git add .
git commit -m "feat: add new feature"
git push origin main
```

### على cPanel (نقرتان فقط):
1. **cPanel → Git Version Control → Manage** (الـ repo اسمه `ppost`)
2. تبويب **Pull or Deploy**:
   - اضغط **Update from Remote** (يجلب آخر تغييرات)
   - اضغط **Deploy HEAD Commit** (يشغّل `.cpanel.yml`)
3. خلال ثوانٍ سترى مخرجات النشر. الموقع مُحدَّث.

---

## المرحلة الرابعة (اختيارية) — أتمتة كاملة عبر Webhook

cPanel يقدّم Webhook URL عند إنشاء الـ repo. أضفه إلى GitHub:

1. cPanel → Git Version Control → Manage → **Webhook URL** (انسخه)
2. GitHub → Settings → Webhooks → **Add webhook**:
   - **Payload URL:** الـ URL الذي نسخته
   - **Content type:** `application/json`
   - **Events:** Just push events
3. الآن: `git push` من جهازك → cPanel يسحب وينشر تلقائياً.

---

## استكشاف الأخطاء

| المشكلة | الحل |
|---------|------|
| `Permission denied (publickey)` | استخدم HTTPS مع Token وليس SSH |
| `.env` ظهر على GitHub | تحقّق من `.gitignore`، احذف الـ commit، غيّر الـ secrets |
| الكاش لا يُمسح بعد النشر | شغّل يدوياً: `php artisan view:clear` و احذف `storage/framework/cache/pages/*.html` |
| 500 Error بعد النشر | راجع `storage/logs/laravel.log`، غالباً صلاحيات أو `.env` ناقص |
| `composer install` يفشل | حدّث الذاكرة المتاحة في cPanel أو شغّله يدوياً |

---

## ملفات هذا النظام

| الملف | الغرض |
|------|------|
| `.gitignore` | يستثني `vendor/`, `.env`, `storage/*` من التتبّع |
| `.cpanel.yml` | يُنفِّذ بعد كل نشر: مسح الكاش، تصاريح، storage:link |
| `DEPLOY.md` | هذا الدليل |

---

**ملاحظة أمان:** لا ترفع توكن GitHub على المستودع. السكربت أعلاه يستخدمه فقط لإعداد الـ remote، ويبقى مخفياً في `.git/config` على cPanel.
