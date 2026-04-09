// شريط الأخبار العاجلة - ppost
// يمكن تحديث الأخبار ديناميكياً من خلال هذا المصفوف
(function () {
    "use strict";

    const breakingNews = [
        "انطلاق قمة اقتصادية دولية لمناقشة الأزمات المالية العالمية",
        "ارتفاع ملحوظ في أسعار النفط بعد قرارات أوبك الأخيرة",
        "فوز المنتخب الوطني في المباراة النهائية للبطولة القارية",
        "إطلاق قمر صناعي جديد لأغراض الاتصالات والبحث العلمي",
        "توقيع اتفاقية تعاون استراتيجي بين عدة دول لمواجهة التغير المناخي"
    ];

    function renderBreakingNews(items) {
        const list = document.getElementById("breakingNewsList");
        if (!list || !Array.isArray(items) || items.length === 0) {
            return;
        }
        list.innerHTML = "";
        items.forEach(function (text) {
            const li = document.createElement("li");
            li.textContent = text;
            list.appendChild(li);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        renderBreakingNews(breakingNews);
    });
})();
