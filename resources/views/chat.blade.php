<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Study Planner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fbf9fa; color: #1b1c1d; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1; }
        #messages-wrap::-webkit-scrollbar { width: 6px; }
        #messages-wrap::-webkit-scrollbar-thumb { background: #e4e2e3; border-radius: 4px; }

        /* ===== SIDEBAR ===== */
        .sidebar{position:fixed;top:0;left:0;bottom:0;width:260px;background:#efedee;display:flex;flex-direction:column;z-index:40;transition:width .2s,transform .2s;border-right:1px solid #d3c2ca}
        .sidebar.collapsed{width:0;overflow:hidden;border:none}
        .sb-header{display:flex;align-items:center;padding:8px 12px;height:48px;gap:4px;flex-shrink:0}
        .sb-header .hamburger{background:none;border:none;color:#645c61;cursor:pointer;padding:8px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:background .12s}
        .sb-header .hamburger:hover{background:#e4e2e3}
        .sb-header .brand{flex:1;display:flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:#1b1c1d;cursor:pointer;padding:6px 8px;border-radius:8px;transition:background .12s;white-space:nowrap}
        .sb-header .brand:hover{background:#e4e2e3}
        .sb-header .brand .material-symbols-outlined{color:#47173c;font-size:18px}
        .sb-header .search-btn{background:none;border:none;color:#645c61;cursor:pointer;padding:8px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:background .12s}
        .sb-header .search-btn:hover{background:#e4e2e3}
        .sb-body{flex:1;overflow-y:auto;padding:4px 8px}
        .new-chat-btn{display:flex;align-items:center;gap:10px;width:100%;padding:10px 12px;border:1px solid #d3c2ca;border-radius:1rem;background:transparent;color:#47173c;font-size:14px;font-weight:600;cursor:pointer;transition:all .12s;margin-bottom:8px}
        .new-chat-btn:hover{background:#e4e2e3}
        .sb-item{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:1rem;cursor:pointer;font-size:13px;color:#645c61;transition:all .1s;position:relative}
        .sb-item:hover{background:#e4e2e3;color:#1b1c1d}
        .sb-item.active{background:#e4e2e3;color:#1b1c1d}

        /* ===== MAIN ===== */
        .main{position:fixed;top:0;left:260px;right:0;bottom:0;display:flex;flex-direction:column;background:#fbf9fa;transition:left .2s}
        .main.full-left{left:0}
        .main-header{padding:12px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}

        /* ===== MESSAGES ===== */
        .messages-wrap{flex:1;overflow-y:auto;display:flex;flex-direction:column}
        .messages{max-width:768px;width:100%;margin:0 auto;padding:16px 24px 24px;flex:1;display:flex;flex-direction:column;justify-content:center}
        .messages.has-chat{justify-content:flex-start}
        .welcome-center{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center}

        /* ===== INPUT ===== */
        .input-bar{padding:0 16px 20px;flex-shrink:0;position:absolute;top:50%;left:0;right:0;z-index:5;transition:all .2s;transform:translateY(-50%)}
        .input-bar.at-bottom{position:relative;top:auto;transform:none}
        .input-wrap{max-width:768px;margin:0 auto;display:flex;align-items:center;gap:0;border:1px solid #d3c2ca;border-radius:1rem;background:#ffffff;padding:6px 6px 6px 18px}
        .input-wrap:focus-within{border-color:#47173c}
        .input-plus{background:none;border:none;color:#645c61;cursor:pointer;padding:8px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:background .12s;flex-shrink:0}
        .input-plus:hover{background:#e4e2e3}
        .input-actions{display:flex;align-items:center;gap:2px;flex-shrink:0}
        .act-btn{background:none;border:none;color:#81737b;cursor:pointer;padding:8px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:all .12s}
        .act-btn:hover{background:#e4e2e3;color:#47173c}
        .mic-btn{background:none;border:none;cursor:pointer;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:all .12s}
        .mic-btn:hover{background:#e4e2e3}
        .mic-btn:disabled{opacity:40%;cursor:not-allowed}
        .mic-btn:disabled:hover{background:none}

        /* ===== OVERLAY ===== */
        .overlay{position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:35;display:none}
        .sidebar:not(.collapsed) ~ .overlay{display:block}
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex font-sans antialiased">

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sb-header">
        <button class="hamburger" onclick="toggleSidebar()" title="Toggle sidebar">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div class="brand" onclick="resetView()">
            <span class="material-symbols-outlined">school</span>
            Study Planner
        </div>
        <button class="search-btn" title="Search">
            <span class="material-symbols-outlined">search</span>
        </button>
    </div>

    <div class="sb-body">
        <button class="new-chat-btn" onclick="createSession()" id="btn-new-chat">
            <span class="material-symbols-outlined">add</span> New chat
        </button>

        <div id="session-list"></div>
    </div>
</aside>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- MAIN -->
<main class="main" id="main">
    <div class="main-header">
        <div class="text-lg font-bold" id="chat-title"></div>
    </div>

    <div class="messages-wrap" id="messages-wrap">
        <div class="messages" id="messages">
            <div class="welcome-center" id="welcome-state">
                <h2 id="welcome-heading" class="text-3xl font-bold mb-3">What's on the agenda today?</h2>
                <p id="welcome-sub" class="text-outline">Study Planner AI siap membantu Anda.</p>
            </div>
        </div>
    </div>

    <div class="input-bar">
        <form id="chat-form" onsubmit="sendMessage(event)">
            <div class="input-wrap">
                <button type="button" class="input-plus" title="Upload file">
                    <span class="material-symbols-outlined">add</span>
                </button>
                <textarea id="chat-input" rows="1" placeholder="Ask anything" oninput="autoResize(this);toggleSend()" class="flex-1 bg-transparent border-none focus:ring-0 p-2 resize-none outline-none"></textarea>
                <div class="input-actions">
                    <button type="button" class="act-btn" title="Think mode">
                        <span class="material-symbols-outlined">psychology</span>
                    </button>
                    <button type="button" class="act-btn" title="Voice input">
                        <span class="material-symbols-outlined">mic</span>
                    </button>
                    <button type="submit" class="mic-btn p-2 text-primary" id="btn-send" disabled title="Send">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </div>
            </div>
        </form>
        <div class="input-note text-center text-xs text-outline mt-3">Study Planner AI dapat membuat kesalahan.</div>
    </div>
</main>

<script>
    const API_BASE='{{ url("/api") }}';
    const SERVER_IS_GUEST=@json($isGuest);
    const SERVER_USER_NAME='{{ addslashes($userName) }}';
    const SERVER_USER_INITIAL='{{ addslashes($userInitial) }}';
    let currentSessionId=null,sessions=[],isGuest=SERVER_IS_GUEST,currentUser=SERVER_IS_GUEST?null:{name:SERVER_USER_NAME};

    function getCsrfToken(){
        const m=document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        return m?decodeURIComponent(m[1]):'';
    }
    function apiFetch(url,opts={}){
        const h=opts.headers||{};
        h['X-XSRF-TOKEN']=getCsrfToken();
        h['Accept']=h['Accept']||'application/json';
        opts.headers=h;opts.credentials=opts.credentials||'same-origin';
        return fetch(url,opts);
    }

    function toggleSend(){
        const input=document.getElementById('chat-input');
        const btn=document.getElementById('btn-send');
        btn.disabled=!input.value.trim();
    }

    function initAuth(){
        if(SERVER_IS_GUEST){
            document.getElementById('btn-new-chat').style.display='none';
            document.getElementById('welcome-heading').textContent="What's on the agenda today?";
            document.getElementById('welcome-sub').textContent='Login untuk menyimpan riwayat percakapan.';
        }else{
            document.getElementById('welcome-heading').textContent=`What's on the agenda today, ${SERVER_USER_NAME}?`;
            document.getElementById('welcome-sub').textContent='Study Planner AI siap membantu Anda.';
        }
        loadSessions();
        resetView();
    }

    async function loadSessions(){
        if(isGuest) return;
        try{
            const res=await apiFetch(`${API_BASE}/chat/sessions`);
            const data=await res.json();
            sessions=data.sessions||[];
            renderSessions();
        }catch(e){}
    }

    function renderSessions(){
        const list=document.getElementById('session-list');
        if(!list) return;
        list.innerHTML=sessions.map(s=>`
            <div class="sb-item ${s.id===currentSessionId?'active':''}" onclick="selectSession('${s.id}')">
                <span class="material-symbols-outlined" style="font-size:16px">chat</span>
                <span class="label">${esc(s.title)}</span>
            </div>
        `).join('');
    }

    async function selectSession(id){
        currentSessionId=id;
        const ws=document.getElementById('welcome-state');
        if(ws) ws.remove();

        const c=document.getElementById('messages');
        c.innerHTML='<div class="p-8 text-center text-outline">Loading...</div>';
        c.classList.add('has-chat');

        try{
            const res=await apiFetch(`${API_BASE}/chat/sessions/${id}/messages`);
            const data=await res.json();
            renderMsgs(data.messages||[]);
            renderSessions();
            setInputMode('bottom');
        }catch(e){
            c.innerHTML='<div class="p-8 text-center text-red-500">Gagal memuat pesan</div>';
        }
    }

    function renderMsgs(msgs){
        const c=document.getElementById('messages');
        if(!msgs.length){c.innerHTML='<div class="p-8 text-center text-outline text-sm">Belum ada pesan.</div>';return;}
        c.innerHTML=msgs.map(m=>`
            <div class="p-4 ${m.role==='user'?'text-right':'text-left'}">
                <div class="inline-block p-3 rounded-2xl ${m.role==='user'?'bg-primary text-on-primary':'bg-surface-container text-on-background'}">
                    ${m.content}
                </div>
            </div>
        `).join('');
        const wrap=document.getElementById('messages-wrap');
        if(wrap) wrap.scrollTop=wrap.scrollHeight;
    }

    async function createSession(){
        if(isGuest){window.location.href='{{ route("login") }}';return;}
        try{
            const res=await apiFetch(`${API_BASE}/chat/session`,{method:'POST',headers:{'Content-Type':'application/json'}});
            const data=await res.json();
            if(data.status==='success'){
                sessions.unshift(data.session);
                renderSessions();
                selectSession(data.session.id);
            }
        }catch(e){}
    }

    function setInputMode(mode){
        const bar=document.querySelector('.input-bar');
        if(mode==='center'){bar.classList.remove('at-bottom');}
        else{bar.classList.add('at-bottom');}
    }

    async function sendMessage(e){
        e.preventDefault();
        const input=document.getElementById('chat-input');
        const msg=input.value.trim();
        if(!msg) return;

        if(!currentSessionId){
            await createSession();
        }

        const sendBtn=document.getElementById('btn-send');
        input.disabled=true;
        sendBtn.disabled=true;

        try{
            const body={message:msg};
            if(currentSessionId) body.chat_session_id=currentSessionId;

            const res=await apiFetch(`${API_BASE}/chat/send`,{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify(body)
            });
            const data=await res.json();

            if(data.status==='success'){
                await selectSession(currentSessionId);
            }

            input.value='';
            input.style.height='auto';
        }catch(e){
            console.error(e);
        }finally{
            input.disabled=false;
            toggleSend();
            input.focus();
        }
    }

    function autoResize(t){t.style.height='auto';t.style.height=Math.min(t.scrollHeight,160)+'px';}
    function toggleSidebar(){document.getElementById('sidebar').classList.toggle('collapsed');document.getElementById('main').classList.toggle('full-left');}
    function resetView(){currentSessionId=null;document.getElementById('messages').innerHTML='<div class="welcome-center" id="welcome-state"><h2 id="welcome-heading" class="text-3xl font-bold mb-3"></h2><p id="welcome-sub" class="text-outline"></p></div>';document.getElementById('messages').classList.remove('has-chat');setInputMode('center');initAuth();}
    function esc(t){const d=document.createElement('div');d.textContent=t;return d.innerHTML;}

    initAuth();
</script>
</body>
</html>