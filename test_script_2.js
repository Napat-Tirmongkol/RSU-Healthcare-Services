
            function triggerGitPull() {
                Swal.fire({
                    title: 'กำลังดำเนินการ Git Pull...',
                    text: 'กรุณารอสักครู่ ระบบกำลังอัปเดตโค้ดล่าสุดจาก Server',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        const btn = document.getElementById('btnGitPull');
                        const btnHistory = document.getElementById('btnGitPullHistory');
                        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
                        if (btnHistory) { btnHistory.disabled = true; btnHistory.style.opacity = '0.6'; }

                        fetch('../admin/ajax_git_pull.php', { method: 'POST' })
                            .then(r => r.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    if (data.detail && !data.detail.includes('Already up to date')) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Git Pull สำเร็จ!',
                                            html: `<div style="text-align:left; font-size:13px; background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0; font-family:monospace; margin-top:10px; max-height:200px; overflow-y:auto;">${data.detail.replace(/\n/g, '<br>')}</div><p style="margin-top:15px; font-weight:700;">รีโหลดหน้าเพื่อใช้งานโค้ดใหม่?</p>`,
                                            showCancelButton: true,
                                            confirmButtonText: 'ตกลง (Reload)',
                                            cancelButtonText: 'ยังไม่รีโหลด',
                                            confirmButtonColor: '#2e9e63'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                location.reload();
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Git Pull สำเร็จ',
                                            text: 'ระบบเป็นเวอร์ชันล่าสุดอยู่แล้ว (Already up to date)',
                                            confirmButtonColor: '#2e9e63'
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Git Pull ล้มเหลว',
                                        text: data.message,
                                        footer: data.detail ? `<pre style="text-align:left; font-size:10px;">${data.detail}</pre>` : ''
                                    });
                                }
                            })
                            .catch((err) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: 'ไม่สามารถเชื่อมต่อกับ AJAX Git Pull ได้'
                                });
                            })
                            .finally(() => {
                                if (btn) { btn.disabled = false; btn.style.opacity = '1'; }
                                if (btnHistory) { btnHistory.disabled = false; btnHistory.style.opacity = '1'; }
                            });
                    }
                });
            }
        