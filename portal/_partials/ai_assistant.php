<?php
// portal/_partials/ai_assistant.php
?>
<div class="h-full flex flex-col bg-white">
    <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-white/80 backdrop-blur-md sticky top-0 z-10">
        <div>
            <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-purple-600"></i> AI Assistant
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">ผู้ช่วยอัจฉริยะวิเคราะห์ข้อมูลแคมเปญด้วย Gemini AI</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="flex items-center gap-2 px-3 py-1.5 bg-purple-50 text-purple-600 rounded-lg border border-purple-100">
                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-wider">Gemini 1.5 Flash</span>
             </div>
        </div>
    </div>

    <div class="flex-1 min-h-0 relative">
        <iframe src="../admin/ai_assistant.php?embed=1" class="w-full h-full border-none" id="aiAssistantIframe"></iframe>
    </div>
</div>

<style>
#section-ai_assistant {
    height: 100vh;
    display: flex;
    flex-direction: column;
}
</style>
