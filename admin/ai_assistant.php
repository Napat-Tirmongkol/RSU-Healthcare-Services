<?php
// admin/ai_assistant.php — Gemini AI Campaign Analyst
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php';

// Quick check: API key configured?
$secretsPath = __DIR__ . '/../config/secrets.php';
$secrets     = file_exists($secretsPath) ? require $secretsPath : [];
$apiKeySet   = !empty($secrets['GEMINI_API_KEY']);

require_once __DIR__ . '/includes/header.php';
renderPageHeader(
    '<i class="fa-solid fa-robot" style="color:#8b5cf6"></i> AI Campaign Analyst',
    'วิเคราะห์ข้อมูลแคมเปญด้วย Gemini AI · ถามอะไรก็ได้เกี่ยวกับข้อมูลในระบบ'
);
?>

<style>
/* ── Chat layout ─────────────────────────────────── */
.chat-wrap {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 20px;
    border: 1.5px solid #ede9fe;
    box-shadow: 0 2px 16px rgba(139,92,246,.08);
    overflow: hidden;
    min-height: 520px;
}
/* ── Message bubbles ─────────────────────────────── */
.msg { display: flex; gap: 12px; align-items: flex-start; }
.msg.user  { flex-direction: row-reverse; }
.msg-avatar {
    width: 36px; height: 36px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; flex-shrink: 0;
}
.msg.user  .msg-avatar { background: linear-gradient(135deg,#8b5cf6,#7c3aed); color:#fff; }
.msg.ai    .msg-avatar { background: linear-gradient(135deg,#1a73e8,#0d47a1); color:#fff; }
.msg-bubble {
    max-width: 82%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: .875rem;
    line-height: 1.65;
}
.msg.user  .msg-bubble {
    background: linear-gradient(135deg,#8b5cf6,#7c3aed);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.msg.ai    .msg-bubble {
    background: #f5f3ff;
    color: #1e1b4b;
    border-bottom-left-radius: 4px;
    border: 1px solid #ede9fe;
}
/* Markdown rendered content */
.msg-bubble h1,.msg-bubble h2,.msg-bubble h3 { font-weight:800; margin:8px 0 4px; }
.msg-bubble h1 { font-size:1.1rem; }
.msg-bubble h2 { font-size:1rem; }
.msg-bubble h3 { font-size:.9rem; }
.msg-bubble p  { margin:4px 0; }
.msg-bubble ul,.msg-bubble ol { padding-left:1.2rem; margin:4px 0; }
.msg-bubble li { margin:2px 0; }
.msg-bubble strong { font-weight:700; }
.msg-bubble table { border-collapse:collapse; width:100%; margin:8px 0; font-size:.8rem; }
.msg-bubble th,.msg-bubble td { border:1px solid #c4b5fd; padding:5px 10px; }
.msg-bubble th { background:#ede9fe; font-weight:700; }
.msg.user .msg-bubble ul,.msg.user .msg-bubble ol { color:rgba(255,255,255,.9); }
.msg.user .msg-bubble th,.msg.user .msg-bubble td { border-color:rgba(255,255,255,.3); }
.msg.user .msg-bubble th { background:rgba(255,255,255,.15); }

/* ── Typing indicator ────────────────────────────── */
.typing-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #8b5cf6;
    animation: typingBounce .9s infinite ease-in-out;
}
.typing-dot:nth-child(2) { animation-delay:.15s; }
.typing-dot:nth-child(3) { animation-delay:.30s; }
@keyframes typingBounce {
    0%,80%,100% { transform:translateY(0); opacity:.4; }
    40%         { transform:translateY(-6px); opacity:1; }
}

/* ── Quick prompt chips ──────────────────────────── */
.prompt-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    background: #f5f3ff; color: #7c3aed;
    border: 1.5px solid #ede9fe;
    border-radius: 99px;
    font-size: .78rem; font-weight: 700;
    cursor: pointer;
    transition: all .18s;
    white-space: nowrap;
}
.prompt-chip:hover { background:#ede9fe; border-color:#c4b5fd; transform:translateY(-1px); }

/* ── Scrollable messages ─────────────────────────── */
#chatMessages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    scrollbar-width: thin;
    scrollbar-color: #c4b5fd transparent;
}
#chatMessages::-webkit-scrollbar { width: 4px; }
#chatMessages::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 99px; }

/* ── Input area ──────────────────────────────────── */
.chat-input-area {
    border-top: 1.5px solid #ede9fe;
    padding: 14px 16px;
    display: flex;
    gap: 10px;
    align-items: flex-end;
    background: #faf8ff;
}
#chatInput {
    flex: 1;
    resize: none;
    border: 1.5px solid #ddd6fe;
    border-radius: 14px;
    padding: 10px 14px;
    font-size: .875rem;
    font-family: 'Prompt', sans-serif;
    outline: none;
    max-height: 120px;
    line-height: 1.5;
    transition: border-color .2s;
    background: #fff;
}
#chatInput:focus { border-color: #8b5cf6; }
#sendBtn {
    width: 42px; height: 42px;
    background: linear-gradient(135deg,#8b5cf6,#7c3aed);
    border: none; border-radius: 12px;
    color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
    transition: all .18s;
    box-shadow: 0 4px 12px rgba(139,92,246,.35);
}
#sendBtn:hover:not(:disabled) { filter:brightness(1.1); transform:translateY(-1px); }
#sendBtn:disabled { opacity:.5; cursor:not-allowed; transform:none; }

/* ── API warning banner ─────────────────────────── */
.api-warn {
    background:#fef3c7; border:1.5px solid #fde68a;
    border-radius:14px; padding:14px 18px;
    display:flex; align-items:flex-start; gap:12px;
    font-size:.875rem; color:#92400e;
}

@media (max-width:639px) {
    .msg-bubble { max-width:90%; font-size:.82rem; }
    .prompt-chip { font-size:.73rem; padding:6px 11px; }
    #chatMessages { padding:14px; gap:12px; }
}
</style>

<?php if (!$apiKeySet): ?>
<div class="api-warn mb-6 fade-up">
    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xl flex-shrink-0 mt-0.5"></i>
    <div>
        <div class="font-bold mb-1">ยังไม่ได้ตั้งค่า Gemini API Key</div>
        <div>เพิ่ม <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono text-xs">GEMINI_API_KEY</code>
        ใน <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono text-xs">config/secrets.php</code>
        แล้วรีเฟรชหน้า<br>
        รับ API Key ฟรีได้ที่ <a href="https://aistudio.google.com/app/apikey" target="_blank" class="underline font-semibold">Google AI Studio</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Prompts -->
<div class="mb-4 fade-up" style="animation-delay:.05s">
    <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">
        <i class="fa-solid fa-bolt mr-1"></i> คำถามด่วน
    </div>
    <div class="flex flex-wrap gap-2">
        <button class="prompt-chip" onclick="sendPrompt(this.dataset.q)"
            data-q="สรุป 10 อันดับแรกที่คนจองเยอะที่สุด พร้อมบอกอัตราการเติมโควต้า">
            <i class="fa-solid fa-ranking-star"></i> Top 10 ยอดนิยม
        </button>
        <button class="prompt-chip" onclick="sendPrompt(this.dataset.q)"
            data-q="วิเคราะห์ภาพรวมของระบบ ว่ามีแคมเปญ การจอง สถานะอะไรบ้าง ระบุจุดที่น่าเป็นห่วง">
            <i class="fa-solid fa-chart-pie"></i> ภาพรวมระบบ
        </button>
        <button class="prompt-chip" onclick="sendPrompt(this.dataset.q)"
            data-q="แคมเปญไหนที่โควต้าใกล้เต็มหรือเต็มแล้ว และแคมเปญไหนที่ยังมีที่ว่างเยอะ แนะนำว่าควรโปรโมตแคมเปญไหน">
            <i class="fa-solid fa-lightbulb"></i> แนะนำแคมเปญที่ควรโปรโมต
        </button>
        <button class="prompt-chip" onclick="sendPrompt(this.dataset.q)"
            data-q="วิเคราะห์แนวโน้มการจอง 7 วันล่าสุด มีทิศทางอย่างไร เพิ่มขึ้นหรือลดลง">
            <i class="fa-solid fa-arrow-trend-up"></i> แนวโน้ม 7 วัน
        </button>
        <button class="prompt-chip" onclick="sendPrompt(this.dataset.q)"
            data-q="อัตราการยกเลิก (cancellation rate) โดยรวมเป็นเท่าไร แคมเปญไหนมีการยกเลิกสูงที่สุด มีข้อเสนอแนะอะไรไหม">
            <i class="fa-solid fa-ban"></i> วิเคราะห์การยกเลิก
        </button>
    </div>
</div>

<!-- Chat Box -->
<div class="chat-wrap fade-up" style="animation-delay:.1s">

    <!-- Messages -->
    <div id="chatMessages">
        <!-- Welcome message -->
        <div class="msg ai" id="welcomeMsg">
            <div class="msg-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="msg-bubble">
                <strong>สวัสดีครับ! ผม AI Campaign Analyst</strong> 🤖<br>
                ผมสามารถวิเคราะห์ข้อมูลแคมเปญ RSU Healthcare ได้แบบเรียลไทม์<br><br>
                ลองกดปุ่ม <strong>คำถามด่วน</strong> ด้านบน หรือพิมพ์คำถามของคุณได้เลย เช่น<br>
                <ul style="margin-top:6px">
                    <li><em>"สรุปข้อมูลแคมเปญ 10 อันดับแรกที่มีคนจองเยอะที่สุด"</em></li>
                    <li><em>"แคมเปญไหนควรเพิ่มโควต้า?"</em></li>
                    <li><em>"ภาพรวมการจองเดือนนี้เป็นอย่างไร?"</em></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Input -->
    <div class="chat-input-area">
        <textarea id="chatInput" rows="1"
            placeholder="พิมพ์คำถามเกี่ยวกับข้อมูลแคมเปญ..."
            onkeydown="handleKey(event)"
            oninput="autoResize(this)"
            <?= !$apiKeySet ? 'disabled' : '' ?>></textarea>
        <button id="sendBtn" onclick="sendMessage()" <?= !$apiKeySet ? 'disabled' : '' ?> title="ส่ง (Enter)">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<!-- marked.js for Markdown rendering -->
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.0/marked.min.js"></script>
<script>
marked.setOptions({ breaks: true, gfm: true });

const chatEl  = document.getElementById('chatMessages');
const inputEl = document.getElementById('chatInput');
const sendEl  = document.getElementById('sendBtn');

// ── Auto-resize textarea ──────────────────────────────────────────────────────
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// ── Keyboard: Enter = send, Shift+Enter = newline ─────────────────────────────
function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

// ── Append message bubble ─────────────────────────────────────────────────────
function appendMsg(role, html) {
    const div = document.createElement('div');
    div.className = 'msg ' + role;
    const icon  = role === 'user' ? 'fa-user' : 'fa-robot';
    div.innerHTML = `
        <div class="msg-avatar"><i class="fa-solid ${icon}"></i></div>
        <div class="msg-bubble">${html}</div>`;
    chatEl.appendChild(div);
    chatEl.scrollTop = chatEl.scrollHeight;
    return div;
}

// ── Typing indicator ──────────────────────────────────────────────────────────
function showTyping() {
    const div = document.createElement('div');
    div.className = 'msg ai';
    div.id = 'typingIndicator';
    div.innerHTML = `
        <div class="msg-avatar"><i class="fa-solid fa-robot"></i></div>
        <div class="msg-bubble" style="padding:14px 18px">
            <div style="display:flex;gap:5px;align-items:center">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <span style="font-size:.75rem;color:#7c3aed;margin-left:4px;font-weight:600">Gemini กำลังคิด…</span>
            </div>
        </div>`;
    chatEl.appendChild(div);
    chatEl.scrollTop = chatEl.scrollHeight;
}
function hideTyping() {
    document.getElementById('typingIndicator')?.remove();
}

// ── Core send ─────────────────────────────────────────────────────────────────
async function sendMessage() {
    const query = inputEl.value.trim();
    if (!query || sendEl.disabled) return;

    // Show user message
    appendMsg('user', escHtml(query).replace(/\n/g, '<br>'));
    inputEl.value = '';
    autoResize(inputEl);
    sendEl.disabled = true;
    showTyping();

    try {
        const fd = new FormData();
        fd.append('query', query);
        fd.append('csrf_token', getCsrf());

        const res  = await fetch('ajax_ai.php', { method: 'POST', body: fd, credentials: 'same-origin' });
        const data = await res.json();

        hideTyping();
        if (data.ok) {
            appendMsg('ai', marked.parse(data.reply));
        } else {
            appendMsg('ai', `<span style="color:#dc2626"><i class="fa-solid fa-circle-exclamation mr-1"></i>${escHtml(data.error)}</span>`);
        }
    } catch (err) {
        hideTyping();
        appendMsg('ai', '<span style="color:#dc2626"><i class="fa-solid fa-circle-exclamation mr-1"></i>เกิดข้อผิดพลาด กรุณาลองใหม่</span>');
    } finally {
        sendEl.disabled = false;
        inputEl.focus();
    }
}

// ── Quick prompt chips ────────────────────────────────────────────────────────
function sendPrompt(text) {
    inputEl.value = text;
    autoResize(inputEl);
    sendMessage();
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function escHtml(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function getCsrf() {
    // ดึง CSRF token จาก hidden field ที่อาจอยู่ในหน้า หรือสร้าง meta tag
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.content;
    // Fallback: ดึงจาก cookie
    const match = document.cookie.match(/csrf_token=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}
</script>

<?php
// Inject CSRF token as meta tag เพื่อให้ JS ดึงได้
echo '<script>document.head.insertAdjacentHTML("beforeend",\'<meta name="csrf-token" content="' . htmlspecialchars(get_csrf_token()) . '">\');</script>';
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
