<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI IT Support</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.8/purify.min.js"></script>

    <style>
        :root {
            --white: #ffffff;
            --bg: #f0f2f5;
            --sidebar-bg: #ffffff;
            --blue: #1d4ed8;
            --blue-hover: #1e40af;
            --blue-light: #eff6ff;
            --blue-mid: #3b82f6;
            --green: #16a34a;
            --red: #dc2626;
            --orange: #ea580c;
            --text: #0f172a;
            --text-2: #334155;
            --text-3: #64748b;
            --text-4: #94a3b8;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --r-sm: 10px;
            --r-md: 14px;
            --r-lg: 20px;
            --sidebar-w: 280px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

        .app { display: flex; height: 100vh; width: 100vw; }
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 18px 16px 14px;
            border-bottom: 1px solid var(--border-light);
        }
        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(29,78,216,.22);
            flex-shrink: 0;
        }
        .brand-name { font-family: 'Sora', sans-serif; font-size: 13.5px; font-weight: 700; color: var(--blue); line-height: 1.2; }
        .brand-sub { font-size: 11px; color: var(--text-4); margin-top: 2px; display: flex; align-items: center; gap: 5px; }
        .dot-online {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { box-shadow: 0 0 0 2px rgba(22,163,74,.2); }
            50% { box-shadow: 0 0 0 4px rgba(22,163,74,.12); }
        }
        .new-btn {
            margin: 12px 12px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 14px;
            border-radius: var(--r-md);
            background: var(--blue);
            color: white;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background .2s, transform .15s;
            box-shadow: 0 3px 12px rgba(29,78,216,.2);
            font-family: 'DM Sans', sans-serif;
        }
        .new-btn:hover { background: var(--blue-hover); transform: translateY(-1px); }
        .search-wrap { padding: 0 12px 10px; position: relative; }
        .search-wrap input {
            width: 100%;
            padding: 7.5px 12px 7.5px 32px;
            border-radius: var(--r-sm);
            border: 1px solid var(--border);
            background: var(--bg);
            font-size: 12.5px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color .2s;
        }
        .search-wrap input:focus { border-color: var(--blue-mid); }
        .search-wrap input::placeholder { color: var(--text-4); }
        .search-ico { position: absolute; left: 22px; top: 50%; transform: translateY(-50%); color: var(--text-4); font-size: 13px; }
        .conv-label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--text-4); padding: 6px 16px; }
        .conv-list { flex: 1; overflow-y: auto; padding: 0 8px; }
        .conv-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 9px;
            border-radius: var(--r-sm);
            cursor: pointer;
            transition: background .15s;
            position: relative;
            margin-bottom: 1px;
        }
        .conv-item:hover { background: var(--bg); }
        .conv-item.active { background: var(--blue-light); border: 1px solid rgba(59,130,246,.15); }
        .conv-item.active .conv-title { color: var(--blue); font-weight: 600; }
        .conv-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .conv-item.active .conv-icon { background: white; border-color: rgba(59,130,246,.2); }
        .conv-info { flex: 1; min-width: 0; }
        .conv-title { font-size: 12.5px; font-weight: 500; color: var(--text-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .conv-meta { font-size: 10.5px; color: var(--text-4); margin-top: 1px; display: flex; align-items: center; gap: 4px; }
        .cbadge { font-size: 9.5px; font-weight: 700; padding: 1.5px 6px; border-radius: 99px; flex-shrink: 0; }
        .cb-urgent { background: #fee2e2; color: var(--red); }
        .cb-escalated { background: #ffedd5; color: var(--orange); }
        .cb-ticket { background: var(--blue-light); color: var(--blue); }
        .conv-more {
            opacity: 0;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-3);
            padding: 3px;
            border-radius: 5px;
            font-size: 14px;
            transition: opacity .15s, background .15s;
        }
        .conv-item:hover .conv-more { opacity: 1; }
        .conv-more:hover { background: var(--border); }
        .ctx-menu {
            position: fixed;
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            box-shadow: 0 12px 40px rgba(0,0,0,.1);
            z-index: 9999;
            min-width: 160px;
            padding: 5px;
            display: none;
        }
        .ctx-menu.show { display: block; animation: fadeUp .12s ease; }
        @keyframes fadeUp {
            from { opacity: 0; transform: scale(.95) translateY(4px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .ctx-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            color: var(--text-2);
            transition: background .12s;
        }
        .ctx-item:hover { background: var(--bg); }
        .ctx-item.danger { color: var(--red); }
        .ctx-item.danger:hover { background: #fee2e2; }
        .sidebar-user {
            border-top: 1px solid var(--border);
            padding: 11px 12px;
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            position: relative;
        }
        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: white;
            font-family: 'Sora', sans-serif;
            flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 12.5px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 10.5px; color: var(--text-4); }
        .user-menu-btn { background: none; border: none; cursor: pointer; color: var(--text-4); padding: 4px; border-radius: 5px; font-size: 14px; }
        .user-dropdown {
            position: absolute;
            bottom: calc(100% + 5px);
            left: 12px;
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            box-shadow: 0 12px 40px rgba(0,0,0,.1);
            min-width: 195px;
            padding: 5px;
            z-index: 999;
            display: none;
        }
        .user-dropdown.show { display: block; animation: fadeUp .12s ease; }
        .dd-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            color: var(--text-2);
            transition: background .12s;
            text-decoration: none;
        }
        .dd-item:hover { background: var(--bg); }
        .dd-item.danger { color: var(--red); }
        .dd-item.danger:hover { background: #fee2e2; }
        .dd-sep { height: 1px; background: var(--border); margin: 4px 0; }
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; height: 100vh; }
        .topbar {
            height: 58px;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 22px;
            flex-shrink: 0;
        }
        .topbar-left { display: flex; align-items: center; gap: 11px; }
        .topbar-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }
        .topbar-title { font-family: 'Sora', sans-serif; font-size: 14.5px; font-weight: 700; color: var(--blue); }
        .topbar-status { display: flex; align-items: center; gap: 4px; font-size: 11.5px; color: var(--text-4); }
        .topbar-right { display: flex; align-items: center; gap: 7px; }
        .tb-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-3);
            font-size: 15px;
            transition: background .15s, color .15s;
        }
        .tb-btn:hover { background: var(--blue-light); color: var(--blue); }
        .model-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4.5px 10px;
            border-radius: 99px;
            background: var(--blue-light);
            border: 1px solid rgba(59,130,246,.18);
            font-size: 11.5px;
            font-weight: 700;
            color: var(--blue);
        }
        .flags-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 22px;
            border-bottom: 1px solid var(--border-light);
            background: white;
            min-height: 36px;
            flex-wrap: wrap;
        }
        .flag-chip {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 11.5px;
            font-weight: 700;
            animation: chipIn .18s ease;
        }
        @keyframes chipIn { from { opacity:0; transform:scale(.85); } to { opacity:1; transform:scale(1); } }
        .flag-urgent { background: #fee2e2; color: var(--red); border: 1px solid rgba(220,38,38,.2); }
        .flag-escalated { background: #ffedd5; color: var(--orange); border: 1px solid rgba(234,88,12,.2); }
        .flag-ticket { background: var(--blue-light); color: var(--blue); border: 1px solid rgba(59,130,246,.2); }
        .flag-remove { cursor: pointer; opacity: .6; font-size: 13px; line-height: 1; transition: opacity .15s; }
        .flag-remove:hover { opacity: 1; }
        .flags-empty { font-size: 11.5px; color: var(--text-4); font-style: italic; }
        .messages-wrap { flex: 1; overflow-y: auto; padding: 24px 0; scroll-behavior: smooth; }
        .messages-inner { max-width: 800px; margin: 0 auto; padding: 0 22px; display: flex; flex-direction: column; gap: 4px; }
        .welcome { display: flex; flex-direction: column; align-items: center; padding: 40px 24px 28px; text-align: center; }
        .welcome-icon {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 22px;
            box-shadow: 0 8px 28px rgba(29,78,216,.2);
        }
        .welcome-title { font-family: 'Sora', sans-serif; font-size: 26px; font-weight: 800; color: var(--blue); margin-bottom: 10px; }
        .welcome-sub { font-size: 14.5px; color: var(--text-3); line-height: 1.65; max-width: 460px; margin-bottom: 28px; }
        .chips-grid { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; max-width: 540px; }
        .chip {
            padding: 7px 14px;
            border-radius: 99px;
            background: white;
            border: 1px solid var(--border);
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-2);
            cursor: pointer;
            transition: all .18s;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .chip:hover { background: var(--blue-light); border-color: rgba(59,130,246,.25); color: var(--blue); transform: translateY(-1px); }
        .day-sep { display: flex; align-items: center; gap: 10px; margin: 10px 0 5px; }
        .day-line { flex: 1; height: 1px; background: var(--border); }
        .day-lbl { font-size: 10.5px; font-weight: 700; color: var(--text-4); letter-spacing: .05em; text-transform: uppercase; }
        .msg-row { display: flex; align-items: flex-start; gap: 8px; animation: msgIn .2s ease; }
        @keyframes msgIn { from { opacity:0; transform:translateY(7px); } to { opacity:1; transform:translateY(0); } }
        .msg-row.user { flex-direction: row-reverse; }
        .msg-av {
            width: 26px;
            height: 26px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .msg-av.bot { background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: white; }
        .msg-av.user { background: linear-gradient(135deg, #7c3aed, #a855f7); color: white; font-weight: 700; font-family: 'Sora', sans-serif; font-size: 10px; }
        .msg-bubble {
            max-width: 68%;
            padding: 11px 15px;
            border-radius: var(--r-md);
            font-size: 14px;
            line-height: 1.7;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .msg-row.bot .msg-bubble {
            background: white;
            border: 1px solid var(--border);
            border-top-left-radius: 4px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            color: #1e3a8a;
            font-weight: 700;
            font-size: 15px;
            line-height: 1.9;
        }
        .msg-row.user .msg-bubble {
            background: var(--blue);
            color: white;
            border-top-right-radius: 4px;
            box-shadow: 0 3px 12px rgba(29,78,216,.2);
            white-space: pre-wrap;
        }
        .msg-bubble h1,.msg-bubble h2,.msg-bubble h3 { font-family: 'Sora', sans-serif; color: var(--blue); margin: 10px 0 5px; }
        .msg-bubble h1 { font-size: 16px; }
        .msg-bubble h2 { font-size: 14.5px; }
        .msg-bubble h3 { font-size: 13.5px; }
        .msg-bubble p { margin-bottom: 6px; }
        .msg-bubble p:last-child { margin-bottom: 0; }
        .msg-bubble ul,.msg-bubble ol { padding-left: 20px; margin: 5px 0; }
        .msg-bubble li { margin-bottom: 3px; }
        .msg-bubble code { background: var(--bg); border: 1px solid var(--border); padding: 1px 5px; border-radius: 5px; font-size: 12.5px; color: var(--blue); }
        .msg-bubble pre { background: #0f172a; border-radius: 9px; padding: 11px 13px; margin: 9px 0; overflow-x: auto; }
        .msg-bubble pre code { background: none; border: none; color: #e2e8f0; font-size: 12.5px; padding: 0; }
        .msg-bubble strong { color: var(--blue); font-weight: 700; }
        .msg-row.bot .msg-bubble strong { color: #2563eb; font-weight: 800; }
        .msg-row.user .msg-bubble strong { color: #bfdbfe; }
        .msg-bubble blockquote { border-left: 3px solid var(--blue-mid); padding-left: 11px; margin: 7px 0; color: var(--text-3); font-style: italic; }
        .msg-bubble img.chat-img { max-width: 100%; max-height: 160px; width: auto; border-radius: 8px; margin-top: 6px; display: block; object-fit: cover; }
        .msg-meta { font-size: 10px; color: var(--text-4); margin-top: 2px; padding: 0 2px; }
        .msg-row.user .msg-meta { text-align: right; }
        .msg-flags { display: flex; gap: 4px; margin-bottom: 5px; flex-wrap: wrap; }
        .mf { display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 99px; font-size: 10.5px; font-weight: 700; }
        .mf-urgent { background: #fee2e2; color: var(--red); }
        .mf-escalated { background: #ffedd5; color: var(--orange); }
        .mf-ticket { background: var(--blue-light); color: var(--blue); }
        .mf-cat { background: #f0fdf4; color: var(--green); }
        .mf-priority { background: #dbeafe; color: #1d4ed8; }
        .typing-row { display: none; align-items: flex-end; gap: 8px; padding: 3px 0; }
        .typing-row.on { display: flex; }
        .typing-bub {
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            border-bottom-left-radius: 4px;
            padding: 13px 16px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .td { width: 6px; height: 6px; border-radius: 50%; background: var(--blue-mid); animation: td 1.3s infinite; }
        .td:nth-child(2) { animation-delay: .18s; }
        .td:nth-child(3) { animation-delay: .36s; }
        @keyframes td { 0%,100% { opacity:.2; transform:scale(.7); } 50% { opacity:1; transform:scale(1); } }
        .input-area {
            border-top: 1px solid var(--border);
            background: rgba(255,255,255,.96);
            backdrop-filter: blur(12px);
            padding: 12px 22px 14px;
            flex-shrink: 0;
        }
        .input-container { max-width: 800px; margin: 0 auto; }
        .action-flags { display: flex; gap: 7px; margin-bottom: 9px; flex-wrap: wrap; }
        .action-flag-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            border-radius: 99px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid var(--border);
            background: white;
            color: var(--text-3);
            cursor: pointer;
            transition: all .18s;
            font-family: 'DM Sans', sans-serif;
        }
        .action-flag-btn:hover { transform: translateY(-1px); }
        .afb-urgent:hover { border-color: var(--red); color: var(--red); background: #fff5f5; }
        .afb-urgent.active { border-color: var(--red); background: var(--red); color: white; box-shadow: 0 3px 10px rgba(220,38,38,.25); }
        .afb-escalated:hover { border-color: var(--orange); color: var(--orange); background: #fff7ed; }
        .afb-escalated.active { border-color: var(--orange); background: var(--orange); color: white; box-shadow: 0 3px 10px rgba(234,88,12,.25); }
        .afb-ticket:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-light); }
        .afb-ticket.active { border-color: var(--blue); background: var(--blue); color: white; box-shadow: 0 3px 10px rgba(29,78,216,.25); }
        .img-prev { display: none; position: relative; margin-bottom: 8px; width: fit-content; }
        .img-prev img { width: 72px; height: 72px; border-radius: 8px; border: 2px solid var(--blue-mid); display: block; object-fit: cover; }
        .img-prev-rm {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--red);
            color: white;
            border: 2px solid white;
            font-size: 9px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .input-box {
            display: flex;
            align-items: flex-end;
            gap: 7px;
            background: white;
            border: 1.5px solid var(--border);
            border-radius: var(--r-lg);
            padding: 9px 9px 9px 15px;
            transition: border-color .2s, box-shadow .2s;
        }
        .input-box:focus-within { border-color: var(--blue-mid); box-shadow: 0 0 0 3px rgba(59,130,246,.09); }
        .input-box.dragging {
            border-color: var(--blue);
            background: var(--blue-light);
            box-shadow: 0 0 0 4px rgba(59,130,246,.12);
        }
        textarea#msg {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            resize: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--text);
            line-height: 1.55;
            max-height: 160px;
            min-height: 22px;
            overflow-y: auto;
        }
        textarea#msg::placeholder { color: var(--text-4); }
        .inp-actions { display: flex; align-items: center; gap: 5px; }
        .inp-btn {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--bg);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-3);
            font-size: 15px;
            transition: all .18s;
        }
        .inp-btn:hover { background: var(--blue-light); color: var(--blue); border-color: rgba(59,130,246,.2); }
        .send-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: none;
            background: var(--blue);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: all .18s;
            box-shadow: 0 2px 8px rgba(29,78,216,.22);
        }
        .send-btn:hover { background: var(--blue-hover); transform: scale(1.06); }
        .send-btn:disabled { opacity: .4; transform: none; cursor: not-allowed; }
        .quick-row { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 9px; }
        .q-chip {
            padding: 4px 11px;
            border-radius: 99px;
            border: 1px solid var(--border);
            background: white;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-2);
            cursor: pointer;
            transition: all .18s;
        }
        .q-chip:hover { background: var(--blue-light); border-color: rgba(59,130,246,.22); color: var(--blue); }
        .inp-hint { text-align: center; font-size: 10.5px; color: var(--text-4); margin-top: 7px; }
        @media (max-width: 820px) {
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 0 12px 40px rgba(0,0,0,.15); }
            .hamburger { display: flex !important; }
            .msg-bubble { max-width: 86%; }
        }
        .hamburger {
            display: none;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            background: none;
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            color: var(--text-3);
            font-size: 16px;
        }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.3); z-index: 99; }
        .overlay.show { display: block; }
        .conv-empty { text-align: center; padding: 18px 12px; color: var(--text-4); font-size: 12.5px; }

        /* Premium support chat enhancements */
        body.dark {
            --white: #0f172a;
            --bg: #0b1120;
            --sidebar-bg: #111827;
            --blue: #60a5fa;
            --blue-hover: #3b82f6;
            --blue-light: rgba(59,130,246,.16);
            --blue-mid: #60a5fa;
            --text: #e5e7eb;
            --text-2: #cbd5e1;
            --text-3: #94a3b8;
            --text-4: #64748b;
            --border: #1f2937;
            --border-light: #172033;
        }
        body.dark .topbar,
        body.dark .flags-bar,
        body.dark .input-area,
        body.dark .sidebar,
        body.dark .ctx-menu,
        body.dark .user-dropdown,
        body.dark .msg-row.bot .msg-bubble,
        body.dark .typing-bub,
        body.dark .chip,
        body.dark .q-chip,
        body.dark .input-box,
        body.dark .action-flag-btn {
            background: var(--sidebar-bg);
        }
        body.dark .msg-row.bot .msg-bubble { color: #bfdbfe; }
        body.dark .msg-row.user .msg-bubble { color: white; }
        body.dark textarea#msg { color: var(--text); }

        .msg-row { margin: 3px 0; }
        .msg-row > div:last-child { max-width: 74%; }
        .msg-row.user > div:last-child { display: flex; flex-direction: column; align-items: flex-end; }
        .msg-row .msg-bubble { max-width: 100%; transition: transform .18s ease, box-shadow .18s ease; }
        .msg-row.bot .msg-bubble {
            border-radius: 16px;
            border-top-left-radius: 5px;
            color: #17316f;
            font-weight: 700;
            font-size: 15px;
            line-height: 1.85;
            box-shadow: 0 10px 28px rgba(15,23,42,.08);
        }
        .msg-row.bot .msg-bubble:hover { box-shadow: 0 14px 32px rgba(15,23,42,.11); }
        .msg-row.user .msg-bubble {
            border-radius: 16px;
            border-top-right-radius: 5px;
            font-weight: 600;
        }
        .msg-bubble a { color: #2563eb; font-weight: 800; }
        .msg-bubble table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 13px; }
        .msg-bubble th,
        .msg-bubble td { border: 1px solid var(--border); padding: 6px 8px; text-align: left; }
        .msg-bubble th { background: var(--blue-light); color: var(--blue); }
        .code-wrap { position: relative; margin: 10px 0; }
        .code-copy {
            position: absolute;
            top: 7px;
            right: 7px;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(15,23,42,.72);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            opacity: 0;
            transition: opacity .15s, transform .15s;
        }
        .code-wrap:hover .code-copy { opacity: 1; }
        .code-copy:active { transform: scale(.96); }
        .msg-bubble pre { margin: 0; padding: 14px; border-radius: 12px; }
        .msg-actions { display: flex; gap: 6px; margin-top: 7px; }
        .mini-btn {
            border: 1px solid var(--border);
            background: var(--white);
            color: var(--text-3);
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transition: all .16s ease;
        }
        .mini-btn:hover { color: var(--blue); background: var(--blue-light); transform: translateY(-1px); }
        .mf-priority.low { background: #dbeafe; color: #1d4ed8; }
        .mf-priority.medium { background: #ffedd5; color: #c2410c; }
        .mf-priority.high,
        .mf-priority.critical { background: #fee2e2; color: #dc2626; }
        .mf-reason { background: #f8fafc; color: #64748b; }
        .toast-stack {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .toast {
            min-width: 260px;
            max-width: 360px;
            padding: 12px 14px;
            border-radius: 14px;
            background: white;
            border: 1px solid var(--border);
            color: var(--text);
            box-shadow: 0 18px 48px rgba(15,23,42,.16);
            font-size: 13px;
            font-weight: 800;
            animation: toastIn .22s ease;
        }
        .toast.ok { border-color: rgba(22,163,74,.28); color: #166534; }
        .toast.err { border-color: rgba(220,38,38,.28); color: #991b1b; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(8px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .typing-bub { box-shadow: 0 8px 22px rgba(15,23,42,.08); }
        .send-btn.loading { pointer-events: none; position: relative; color: transparent; }
        .send-btn.loading::after {
            content: '';
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255,255,255,.55);
            border-top-color: white;
            border-radius: 50%;
            position: absolute;
            animation: spin .75s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .tb-btn.active { background: var(--blue); color: white; border-color: var(--blue); }
        .net-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4.5px 10px;
            border-radius: 99px;
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid rgba(22,163,74,.2);
            font-size: 11px;
            font-weight: 800;
        }
        .net-pill.off {
            background: #fee2e2;
            color: #b91c1c;
            border-color: rgba(220,38,38,.24);
        }
        .drop-hint {
            display: none;
            position: fixed;
            inset: 14px;
            z-index: 9998;
            border: 2px dashed var(--blue-mid);
            border-radius: 24px;
            background: rgba(239,246,255,.86);
            color: var(--blue);
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            backdrop-filter: blur(8px);
            pointer-events: none;
        }
        .drop-hint.show { display: flex; }
        @media (max-width: 640px) {
            .topbar { padding: 0 12px; }
            .topbar-title { font-size: 13px; }
            .model-pill { display: none; }
            .messages-wrap { padding: 16px 0; }
            .messages-inner { padding: 0 12px; }
            .msg-row > div:last-child { max-width: 88%; }
            .msg-bubble { padding: 10px 12px; font-size: 13.5px; }
            .input-area { padding: 10px 10px 12px; }
            .action-flags { gap: 5px; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 2px; }
            .action-flag-btn { white-space: nowrap; padding: 6px 10px; }
            .quick-row { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 3px; }
            .q-chip { white-space: nowrap; }
            .toast-stack { left: 12px; right: 12px; bottom: 12px; }
            .toast { min-width: 0; max-width: none; }
        }
    </style>
</head>
<body>
<div class="app">
    <div class="overlay" id="overlay"></div>

    <div class="ctx-menu" id="ctxMenu">
        <div class="ctx-item" onclick="renameConv()">Renommer</div>
        <div class="ctx-item danger" onclick="deleteConv()">Supprimer</div>
    </div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">🤖</div>
            <div>
                <div class="brand-name">AI IT Support</div>
                <div class="brand-sub"><span class="dot-online"></span> Assistant actif</div>
            </div>
        </div>

        <button class="new-btn" id="newChatBtn">+ Nouvelle conversation</button>

        <div class="search-wrap">
            <span class="search-ico">⌕</span>
            <input type="text" id="convSearch" placeholder="Rechercher...">
        </div>

        <div class="conv-label">Conversations</div>
        <div class="conv-list" id="convList"><div class="conv-empty">Aucune conversation</div></div>

        <div class="sidebar-user" id="sidebarUser">
            <div class="avatar">{{ substr(auth()->user()->name, 0, 2) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Utilisateur IT</div>
            </div>
            <button class="user-menu-btn" id="userMenuBtn">⋮</button>
            <div class="user-dropdown" id="userDropdown">
                <a href="/dashboard" class="dd-item">Dashboard</a>
                <div class="dd-sep"></div>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <div class="dd-item danger" onclick="document.getElementById('logoutForm').submit()">Déconnexion</div>
                </form>
            </div>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="hamburger" id="hamburger">☰</button>
                <div class="topbar-icon">🤖</div>
                <div>
                    <div class="topbar-title">AI IT Support Assistant</div>
                    <div class="topbar-status"><span class="dot-online" style="width:5px;height:5px;margin:0;"></span> Vision AI · Analyse intelligente</div>
                </div>
            </div>
            <div class="topbar-right">
                <div class="net-pill" id="netPill">● Online</div>
                
                <button class="tb-btn" id="exportBtn" title="Exporter conversation">⇩</button>
                <button class="tb-btn" id="clearBtn" title="Vider conversation">⌫</button>
                <button class="tb-btn" id="darkToggle" title="Mode sombre">☾</button>
                <button class="tb-btn" title="Paramètres" onclick="alert('Settings à configurer')">⚙</button>
            </div>
        </div>

        <div class="flags-bar" id="flagsBar">
            <span class="flags-empty">Aucun flag actif — utilisez les boutons ci-dessous</span>
        </div>

        <div class="messages-wrap" id="msgsWrap">
            <div class="messages-inner" id="msgsInner">
                <div class="welcome" id="welcome">
                    <div class="welcome-icon">🤖</div>
                    <div class="welcome-title">Bonjour {{ auth()->user()->name }} 👋</div>
                    <p class="welcome-sub">
                        Décrivez votre problème informatique ou envoyez un screenshot.<br>
                        Activez les flags 🚨 Urgent · ⬆️ Escalade · 🎫 Ticket si nécessaire.
                    </p>
                    <div class="chips-grid">
                        <div class="chip" onclick="setQ('Mon wifi ne fonctionne pas')">📶 WiFi</div>
                        <div class="chip" onclick="setQ(&quot;Mon PC est très lent, comment l'optimiser ?&quot;)">🐢 PC Lent</div>
                        <div class="chip" onclick="setQ(&quot;Erreur imprimante impossible d'imprimer&quot;)">🖨️ Imprimante</div>
                        <div class="chip" onclick="setQ('Impossible de me connecter à mon compte')">🔐 Connexion</div>
                        <div class="chip" onclick="setQ('Problème avec Microsoft 365 Outlook')">📧 Microsoft 365</div>
                        <div class="chip" onclick="setQ(&quot;Mon écran ne s'allume pas&quot;)">🖥️ Écran noir</div>
                        <div class="chip" onclick="setQ('Problème réseau VPN')">🔒 VPN</div>
                        <div class="chip" onclick="setQ('Mise à jour Windows échouée')">⚙️ Windows Update</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 0 22px;">
            <div style="max-width:800px;margin:0 auto;">
                <div class="typing-row" id="typingRow">
                    <div class="msg-av bot">🤖</div>
                    <div class="typing-bub">
                        <div class="td"></div><div class="td"></div><div class="td"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="input-area">
            <div class="input-container">
                <div class="action-flags">
                    <button class="action-flag-btn afb-urgent" id="btnUrgent" onclick="toggleFlag('urgent')">🚨 <span>Urgent</span></button>
                    <button class="action-flag-btn afb-escalated" id="btnEscalated" onclick="toggleFlag('escalated')">⬆️ <span>Escalade</span></button>
                    <button class="action-flag-btn afb-ticket" id="btnTicket" onclick="toggleFlag('ticket')">🎫 <span>Créer Ticket</span></button>
                </div>
                <div class="img-prev" id="imgPrev">
                    <img id="prevImg" alt="preview">
                    <div class="img-prev-rm" onclick="removeImg()">×</div>
                </div>
                <div class="input-box">
                    <textarea id="msg" placeholder="Décrivez votre problème informatique..." rows="1"></textarea>
                    <div class="inp-actions">
                        <label class="inp-btn" title="Joindre image">
                            📎
                            <input type="file" id="imgInput" accept="image/*" hidden>
                        </label>
                        <button type="button" class="inp-btn" id="voiceBtn" title="Dictée vocale">🎙</button>
                        <button class="send-btn" id="sendBtn" title="Envoyer">➤</button>
                    </div>
                </div>
                <div class="quick-row">
                    <div class="q-chip" onclick="setQ('Mon wifi ne fonctionne pas')">WiFi</div>
                    <div class="q-chip" onclick="setQ('Mon PC est très lent')">PC Lent</div>
                    <div class="q-chip" onclick="setQ('Erreur imprimante')">Imprimante</div>
                    <div class="q-chip" onclick="setQ('Impossible de me connecter')">Login</div>
                    <div class="q-chip" onclick="setQ('Problème réseau VPN')">VPN</div>
                    <div class="q-chip" onclick="setQ('Mise à jour Windows échouée')">Windows Update</div>
                </div>
                <div class="inp-hint">Entrée pour envoyer · Maj+Entrée pour saut de ligne</div>
            </div>
        </div>
    </main>
</div>
<div class="drop-hint" id="dropHint">Déposez votre screenshot ici</div>
<div class="toast-stack" id="toastStack"></div>

<script>
const STORE = 'it_conv_v2';
const ACTIVE_KEY = 'it_active_conv';
const USER_NAME = '{{ auth()->user()->name }}';
const USER_ID = '{{ auth()->user()->id }}';
const INITIALS = USER_NAME.substring(0, 2).toUpperCase();
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let conversations = {};
let activeId = null;
let ctxTargetId = null;
const flags = { urgent: false, escalated: false, ticket: false };

const msgsInner = document.getElementById('msgsInner');
const msgsWrap = document.getElementById('msgsWrap');
const welcome = document.getElementById('welcome');
const typingRow = document.getElementById('typingRow');
const textarea = document.getElementById('msg');
const imgInput = document.getElementById('imgInput');
const imgPrev = document.getElementById('imgPrev');
const prevImg = document.getElementById('prevImg');
const sendBtn = document.getElementById('sendBtn');
const convList = document.getElementById('convList');
const convSearch = document.getElementById('convSearch');
const ctxMenu = document.getElementById('ctxMenu');
const userDD = document.getElementById('userDropdown');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const flagsBar = document.getElementById('flagsBar');
const toastStack = document.getElementById('toastStack');
const darkToggle = document.getElementById('darkToggle');
const exportBtn = document.getElementById('exportBtn');
const clearBtn = document.getElementById('clearBtn');
const netPill = document.getElementById('netPill');
const dropHint = document.getElementById('dropHint');
const inputBox = document.querySelector('.input-box');
let lastRetryPayload = null;

function load() {
    try { conversations = JSON.parse(localStorage.getItem(STORE) || '{}'); } catch { conversations = {}; }
    cleanupOldConversations();
    activeId = localStorage.getItem(ACTIVE_KEY) || null;
}
function save() {
    localStorage.setItem(STORE, JSON.stringify(conversations));
    localStorage.setItem(ACTIVE_KEY, activeId || '');
}
function cleanupOldConversations() {
    const cutoff = Date.now() - 1000 * 60 * 60 * 24 * 45;
    Object.keys(conversations).forEach(id => {
        if ((conversations[id].updatedAt || 0) < cutoff) delete conversations[id];
    });
}
function genId() { return 'c_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5); }
function fmtTime(ts) { return new Date(ts).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }); }
function fmtDate(ts) {
    const d = new Date(ts), t = new Date(), y = new Date(t);
    y.setDate(t.getDate() - 1);
    if (d.toDateString() === t.toDateString()) return "Aujourd'hui";
    if (d.toDateString() === y.toDateString()) return 'Hier';
    return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
}
function scrollBot(smooth = true) { msgsWrap.scrollTo({ top: msgsWrap.scrollHeight, behavior: smooth ? 'smooth' : 'auto' }); }
function esc(str) { return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function normBool(value) {
    return value === true || value === 1 || value === '1' || value === 'true' || value === 'yes';
}
function cleanText(value) {
    return typeof value === 'string' ? value.trim() : '';
}
function parseMaybeJson(value) {
    if (typeof value !== 'string') return value;
    const trimmed = value.trim();
    if (!trimmed || !['{', '['].includes(trimmed[0])) return value;
    try { return JSON.parse(trimmed); } catch { return value; }
}
function getPath(obj, path) {
    return path.split('.').reduce((acc, key) => acc && typeof acc === 'object' ? acc[key] : undefined, obj);
}
function extractTextDeep(value, depth = 0) {
    if (depth > 4 || value == null) return '';
    value = parseMaybeJson(value);
    if (typeof value === 'string') return cleanText(value);
    if (Array.isArray(value)) {
        for (const item of value) {
            const found = extractTextDeep(item, depth + 1);
            if (found) return found;
        }
        return '';
    }
    if (typeof value !== 'object') return '';

    const paths = [
        'solution',
        'response.solution',
        'data.solution',
        'message',
        'response.message',
        'data.message',
        'ai_response',
        'response.ai_response',
        'data.ai_response',
        'response',
        'data.response',
        'output.message',
        'output',
        'answer',
        'result.message',
        'result',
        'text'
    ];

    for (const path of paths) {
        const found = extractTextDeep(getPath(value, path), depth + 1);
        if (found) return found;
    }

    return '';
}
function normalizeWebhookData(data) {
    const root = parseMaybeJson(data);
    const safeRoot = root && typeof root === 'object' ? root : {};
    const dataObj = safeRoot.data && typeof safeRoot.data === 'object' && !Array.isArray(safeRoot.data) ? safeRoot.data : {};
    const responseObj = safeRoot.response && typeof safeRoot.response === 'object' && !Array.isArray(safeRoot.response) ? safeRoot.response : {};
    const botText = extractTextDeep(safeRoot) || 'Reponse IA indisponible. Merci de reessayer.';

    const metaSource = Object.keys(dataObj).length ? dataObj : (Object.keys(responseObj).length ? responseObj : safeRoot);
    const ticketId = dataObj.ticket_id || responseObj.ticket_id || safeRoot.ticket_id || null;
    const jiraTicketId = dataObj.jira_ticket_id || responseObj.jira_ticket_id || safeRoot.jira_ticket_id || ticketId || null;

    return {
        text: botText,
        category: dataObj.category || responseObj.category || safeRoot.category || 'general',
        priority: dataObj.priority || responseObj.priority || safeRoot.priority || 'medium',
        status: dataObj.status || responseObj.status || safeRoot.status || 'resolved',
        isUrgent: normBool(dataObj.is_urgent ?? responseObj.is_urgent ?? safeRoot.is_urgent),
        isEscalated: normBool(dataObj.is_escalated ?? responseObj.is_escalated ?? safeRoot.is_escalated),
        hasTicket: normBool(dataObj.create_ticket ?? responseObj.create_ticket ?? safeRoot.create_ticket) || Boolean(ticketId || jiraTicketId),
        reason: dataObj.reason || responseObj.reason || safeRoot.reason || null,
        ticketId,
        jiraTicketId,
        conversationId: dataObj.conversation_id || responseObj.conversation_id || safeRoot.conversation_id || null,
        raw: safeRoot,
        meta: metaSource
    };
}
function renderBotMarkdown(text) {
    if (!cleanText(text)) return '';
    const html = marked.parse(text);
    return window.DOMPurify
        ? DOMPurify.sanitize(html, { ADD_ATTR: ['target', 'rel'] })
        : html;
}
function enhanceCodeBlocks(scope = msgsInner) {
    scope.querySelectorAll('pre code').forEach(code => {
        if (window.hljs && !code.dataset.hlDone) {
            hljs.highlightElement(code);
            code.dataset.hlDone = '1';
        }
        const pre = code.closest('pre');
        if (!pre || pre.parentElement.classList.contains('code-wrap')) return;
        const wrap = document.createElement('div');
        wrap.className = 'code-wrap';
        const btn = document.createElement('button');
        btn.className = 'code-copy';
        btn.type = 'button';
        btn.textContent = 'Copier';
        btn.addEventListener('click', async () => {
            await navigator.clipboard.writeText(code.innerText);
            btn.textContent = 'Copié';
            showToast('Code copié', 'ok');
            setTimeout(() => btn.textContent = 'Copier', 1200);
        });
        pre.parentNode.insertBefore(wrap, pre);
        wrap.appendChild(pre);
        wrap.appendChild(btn);
    });
}
function showToast(message, type = 'ok') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    toastStack.appendChild(toast);
    setTimeout(() => toast.remove(), 3600);
}
function setSending(on) {
    sendBtn.disabled = on;
    sendBtn.classList.toggle('loading', on);
}

function toggleFlag(type) {
    flags[type] = !flags[type];
    document.getElementById('btn' + type.charAt(0).toUpperCase() + type.slice(1)).classList.toggle('active', flags[type]);
    renderFlagsBar();
}
function renderFlagsBar() {
    const active = Object.entries(flags).filter(([,v]) => v);
    if (!active.length) {
        flagsBar.innerHTML = '<span class="flags-empty">Aucun flag actif — utilisez les boutons ci-dessous</span>';
        return;
    }
    const labels = { urgent: '🚨 URGENT', escalated: '⬆️ ESCALADÉ', ticket: '🎫 TICKET' };
    const cls = { urgent: 'flag-urgent', escalated: 'flag-escalated', ticket: 'flag-ticket' };
    flagsBar.innerHTML = active.map(([k]) =>
        `<div class="flag-chip ${cls[k]}">${labels[k]}<span class="flag-remove" onclick="toggleFlag('${k}')">×</span></div>`
    ).join('') + `<span style="font-size:11.5px;color:var(--text-4);margin-left:4px">Actifs pour le prochain message</span>`;
}

function renderConvList(filter = '') {
    const ids = Object.keys(conversations).sort((a,b) => (conversations[b].updatedAt || 0) - (conversations[a].updatedAt || 0));
    const filtered = filter ? ids.filter(id => conversations[id].title.toLowerCase().includes(filter.toLowerCase())) : ids;
    if (!filtered.length) {
        convList.innerHTML = '<div class="conv-empty">Aucune conversation</div>';
        return;
    }
    convList.innerHTML = filtered.map(id => {
        const c = conversations[id], isActive = id === activeId;
        let badges = '';
        if (c.hasUrgent) badges += '<span class="cbadge cb-urgent">🔴 Urgent</span>';
        if (c.hasEscalated) badges += '<span class="cbadge cb-escalated">🟠 Escaladé</span>';
        if (c.hasTicket) badges += '<span class="cbadge cb-ticket">🎫 Ticket</span>';
        if (c.priority && c.priority !== 'medium') badges += `<span class="cbadge cb-ticket">${esc(c.priority)}</span>`;
        if (!badges && c.category) badges = `<span class="cbadge cb-ticket">${esc(c.category)}</span>`;
        return `<div class="conv-item ${isActive ? 'active' : ''}" onclick="loadConv('${id}')" data-id="${id}">
            <div class="conv-icon">${c.icon || '💬'}</div>
            <div class="conv-info">
                <div class="conv-title">${esc(c.title)}</div>
                <div class="conv-meta">${fmtDate(c.updatedAt)} ${badges}</div>
            </div>
            <button class="conv-more" onclick="openCtx(event,'${id}')">⋮</button>
        </div>`;
    }).join('');
}

function clearMsgs() { msgsInner.querySelectorAll('.msg-row,.day-sep,.msg-meta').forEach(el => el.remove()); }
function showWelcome(show) { welcome.style.display = show ? 'flex' : 'none'; }
function renderMessages(msgs) {
    clearMsgs();
    if (!msgs || !msgs.length) {
        showWelcome(true);
        return;
    }
    showWelcome(false);
    let lastDate = null;
    msgs.forEach(msg => {
        const dl = fmtDate(msg.ts);
        if (dl !== lastDate) {
            lastDate = dl;
            const sep = document.createElement('div');
            sep.className = 'day-sep';
            sep.innerHTML = `<div class="day-line"></div><div class="day-lbl">${dl}</div><div class="day-line"></div>`;
            msgsInner.appendChild(sep);
        }
        appendMsg(msg, false);
    });
    scrollBot(false);
}

function appendMsg(msg, animated = true) {
    showWelcome(false);
    const row = document.createElement('div');
    row.className = `msg-row ${msg.role}`;
    if (!animated) row.style.animation = 'none';

    const av = msg.role === 'bot'
        ? `<div class="msg-av bot">🤖</div>`
        : `<div class="msg-av user">${INITIALS}</div>`;

    let content = '';
    if (msg.image) content += `<img class="chat-img" src="${msg.image}" alt="Screenshot">`;
    if (cleanText(msg.text)) content += msg.role === 'bot' ? renderBotMarkdown(msg.text) : esc(msg.text);
    if (!content) content = msg.role === 'bot' ? '<p>Reponse IA indisponible. Merci de reessayer.</p>' : '';

    let flagHtml = '';
    if (msg.isUrgent) flagHtml += '<span class="mf mf-urgent">🚨 URGENT</span>';
    if (msg.isEscalated) flagHtml += '<span class="mf mf-escalated">⬆️ ESCALADÉ</span>';
    if (msg.hasTicket) flagHtml += '<span class="mf mf-ticket">🎫 TICKET</span>';
    if (msg.jiraTicketId || msg.ticketId) flagHtml += `<span class="mf mf-ticket"># ${esc(msg.jiraTicketId || msg.ticketId)}</span>`;
    if (msg.status && msg.status !== 'resolved') flagHtml += `<span class="mf mf-cat">● ${esc(msg.status)}</span>`;
    if (msg.priority) flagHtml += `<span class="mf mf-priority ${esc(msg.priority).toLowerCase()}">⚡ ${esc(msg.priority).toUpperCase()}</span>`;
    if (msg.category && msg.category !== 'general') flagHtml += `<span class="mf mf-cat">📁 ${esc(msg.category)}</span>`;
    if (msg.reason && msg.showReason) flagHtml += `<span class="mf mf-reason">ⓘ ${esc(msg.reason)}</span>`;
    const flagsDiv = flagHtml ? `<div class="msg-flags">${flagHtml}</div>` : '';
    const actions = msg.error
        ? `<div class="msg-actions"><button class="mini-btn" type="button" onclick="retryLastMessage()">Réessayer</button></div>`
        : '';

    row.innerHTML = `${av}<div>${flagsDiv}<div class="msg-bubble">${content}</div>${actions}</div>`;
    msgsInner.appendChild(row);

    const meta = document.createElement('div');
    meta.className = 'msg-meta';
    meta.style.cssText = msg.role === 'user' ? 'text-align:right;padding-right:36px' : 'padding-left:36px';
    meta.textContent = fmtTime(msg.ts);
    msgsInner.appendChild(meta);

    if (animated) scrollBot();
    if (msg.role === 'bot') enhanceCodeBlocks(row);
}

function createNewConv() {
    const id = genId();
    conversations[id] = {
        id,
        backendId: null,
        title: 'Nouvelle conversation',
        icon: '💬',
        messages: [],
        createdAt: Date.now(),
        updatedAt: Date.now(),
        hasUrgent: false,
        hasEscalated: false,
        hasTicket: false,
        category: null
    };
    activeId = id;
    save();
    renderConvList();
    renderMessages([]);
    textarea.focus();
    closeSidebar();
}
function loadConv(id) {
    activeId = id;
    save();
    renderConvList();
    renderMessages(conversations[id]?.messages || []);
    closeSidebar();
}
function addMsgToConv(msg) {
    if (!activeId) createNewConv();
    const c = conversations[activeId];
    c.messages.push(msg);
    c.updatedAt = Date.now();
    if (msg.conversationId && /^\d+$/.test(String(msg.conversationId))) c.backendId = Number(msg.conversationId);
    if (c.messages.length === 1 && msg.role === 'user' && msg.text) {
        c.title = msg.text.substring(0, 40) + (msg.text.length > 40 ? '…' : '');
        c.icon = msg.isUrgent ? '🚨' : msg.isEscalated ? '⬆️' : msg.hasTicket ? '🎫' : '💬';
    }
    if (msg.isUrgent) c.hasUrgent = true;
    if (msg.isEscalated) c.hasEscalated = true;
    if (msg.hasTicket) c.hasTicket = true;
    if (msg.category) c.category = msg.category;
    if (msg.priority) c.priority = msg.priority;
    if (msg.status) c.status = msg.status;
    if (msg.ticketId) c.ticketId = msg.ticketId;
    if (msg.jiraTicketId) c.jiraTicketId = msg.jiraTicketId;
    save();
    renderConvList();
}

async function sendMessage() {
    const text = textarea.value.trim();
    const file = imgInput.files[0];
    if (!text && !file) return;

    if (!activeId) createNewConv();

    let imgDataUrl = null;
    if (file) {
        try {
            validateImageFile(file);
            imgDataUrl = await readFile(file);
        } catch (err) {
            showToast(err.message || 'Image invalide.', 'err');
            return;
        }
    }

    const snapshot = { urgent: flags.urgent, escalated: flags.escalated, ticket: flags.ticket };
    const userMsg = {
        role: 'user',
        text: text || '',
        image: imgDataUrl,
        ts: Date.now(),
        isUrgent: snapshot.urgent,
        isEscalated: snapshot.escalated,
        hasTicket: snapshot.ticket
    };

    addMsgToConv(userMsg);
    appendMsg(userMsg);

    textarea.value = '';
    textarea.style.height = 'auto';
    imgInput.value = '';
    imgPrev.style.display = 'none';
    setSending(true);

    flags.urgent = false;
    flags.escalated = false;
    flags.ticket = false;
    ['btnUrgent','btnEscalated','btnTicket'].forEach(id => document.getElementById(id).classList.remove('active'));
    renderFlagsBar();

    typingRow.classList.add('on');
    scrollBot();

    const formData = new FormData();
    formData.append('message', text);
    formData.append('source', 'web');
    const backendConversationId = conversations[activeId]?.backendId;
    if (backendConversationId) formData.append('conversation_id', backendConversationId);
    formData.append('user_id', USER_ID);
    formData.append('is_urgent', snapshot.urgent ? '1' : '0');
    formData.append('is_escalated', snapshot.escalated ? '1' : '0');
    formData.append('create_ticket', snapshot.ticket ? '1' : '0');
    if (file) {
        try {
            formData.append('image', await prepareImageFile(file));
        } catch (err) {
            typingRow.classList.remove('on');
            pushBotErr(err.message || 'Image invalide.');
            return;
        }
    }
    lastRetryPayload = { text, imgDataUrl, snapshot };

    try {
        const res = await fetch('/api/webhook/support', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: formData
        });

        const raw = await res.text();
        console.log('RAW RESPONSE:', raw);
        typingRow.classList.remove('on');

        if (!raw || raw.trim() === '') {
            pushBotErr('Réponse vide du serveur. Vérifiez que le workflow n8n répond bien au webhook.');
            return;
        }
        if (raw.includes('<!DOCTYPE') || raw.includes('<html')) {
            pushBotErr('Erreur serveur HTML reçue. Vérifiez les logs Laravel et la route /api/webhook/support.');
            return;
        }

        let data;
        try {
            data = JSON.parse(raw);
        } catch {
            pushBotErr('Réponse serveur invalide. Le webhook doit retourner du JSON valide.');
            return;
        }
        console.log('PARSED DATA:', data);

        if (data.success === false) {
            pushBotErr(data.message || data.error || 'Le serveur a refusé la demande.');
            return;
        }

        const parsed = normalizeWebhookData(data);
        console.log('BOT TEXT:', parsed.text);

        const botMsg = {
            role: 'bot',
            text: parsed.text,
            ts: Date.now(),
            category: parsed.category,
            priority: parsed.priority,
            status: parsed.status,
            isUrgent: parsed.isUrgent,
            isEscalated: parsed.isEscalated,
            hasTicket: parsed.hasTicket,
            reason: parsed.reason,
            ticketId: parsed.ticketId,
            jiraTicketId: parsed.jiraTicketId,
            conversationId: parsed.conversationId,
            raw: parsed.raw
        };

        addMsgToConv(botMsg);
        appendMsg(botMsg);

        if (botMsg.hasTicket || botMsg.ticketId || botMsg.jiraTicketId) {
            showToast(`Ticket créé avec succès${botMsg.jiraTicketId ? ' : ' + botMsg.jiraTicketId : ''}`, 'ok');
        }
    } catch (err) {
        typingRow.classList.remove('on');
        pushBotErr('Erreur de connexion au serveur. Vérifiez Laravel, n8n et la route webhook.');
        console.error(err);
    }

    setSending(false);
}

function pushBotErr(text) {
    const m = { role: 'bot', text, ts: Date.now(), error: true, priority: 'high', category: 'error' };
    addMsgToConv(m);
    appendMsg(m);
    showToast(text, 'err');
    setSending(false);
}
function retryLastMessage() {
    if (!lastRetryPayload || !lastRetryPayload.text) {
        showToast('Aucun message à réessayer', 'err');
        return;
    }
    textarea.value = lastRetryPayload.text;
    textarea.dispatchEvent(new Event('input'));
    showToast('Message restauré. Cliquez sur envoyer pour réessayer.', 'ok');
    textarea.focus();
}
function validateImageFile(file) {
    if (!file) return;
    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowed.includes(file.type)) throw new Error('Format image non supporte. Utilisez JPG, PNG ou WEBP.');
    if (file.size > 5 * 1024 * 1024) throw new Error('Image trop grande. Maximum 5 MB.');
}
function prepareImageFile(file) {
    validateImageFile(file);
    if (file.size <= 1.8 * 1024 * 1024 || file.type === 'image/webp') return Promise.resolve(file);

    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const maxSide = 1400;
            const scale = Math.min(1, maxSide / Math.max(img.width, img.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(img.width * scale);
            canvas.height = Math.round(img.height * scale);
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(blob => {
                if (!blob) return reject(new Error('Compression image impossible.'));
                resolve(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' }));
            }, 'image/jpeg', .82);
        };
        img.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error('Image illisible.'));
        };
        img.src = url;
    });
}
function readFile(file) {
    return new Promise((res, rej) => {
        const r = new FileReader();
        r.onload = e => res(e.target.result);
        r.onerror = () => rej(new Error('Read failed'));
        r.readAsDataURL(file);
    });
}

sendBtn.addEventListener('click', sendMessage);
textarea.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});
textarea.addEventListener('input', () => {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 160) + 'px';
});
['dragenter', 'dragover'].forEach(evt => {
    document.addEventListener(evt, e => {
        e.preventDefault();
        inputBox.classList.add('dragging');
        dropHint.classList.add('show');
    });
});
['dragleave', 'drop'].forEach(evt => {
    document.addEventListener(evt, e => {
        e.preventDefault();
        inputBox.classList.remove('dragging');
        dropHint.classList.remove('show');
    });
});
document.addEventListener('drop', e => {
    const file = [...(e.dataTransfer?.files || [])].find(f => f.type.startsWith('image/'));
    if (file) setImageFile(file);
});
imgInput.addEventListener('change', async function () {
    const f = this.files[0];
    if (!f) {
        imgPrev.style.display = 'none';
        return;
    }
    try {
        validateImageFile(f);
        prevImg.src = await readFile(f);
        imgPrev.style.display = 'block';
    } catch (err) {
        removeImg();
        showToast(err.message || 'Image invalide.', 'err');
    }
});
function removeImg() {
    imgInput.value = '';
    imgPrev.style.display = 'none';
}
function setQ(text) {
    textarea.value = text;
    textarea.focus();
    textarea.dispatchEvent(new Event('input'));
}
async function setImageFile(file) {
    try {
        validateImageFile(file);
        const dt = new DataTransfer();
        dt.items.add(file);
        imgInput.files = dt.files;
        prevImg.src = await readFile(file);
        imgPrev.style.display = 'block';
        showToast('Screenshot ajoute', 'ok');
    } catch (err) {
        showToast(err.message || 'Image invalide.', 'err');
    }
}

const voiceBtn = document.getElementById('voiceBtn');
if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    const rec = new SR();
    rec.lang = 'fr-FR';
    rec.continuous = false;
    rec.interimResults = false;
    voiceBtn.addEventListener('click', () => {
        rec.start();
        voiceBtn.style.background = '#fee2e2';
        voiceBtn.style.color = '#dc2626';
    });
    rec.onresult = e => {
        textarea.value = e.results[0][0].transcript;
        textarea.dispatchEvent(new Event('input'));
    };
    rec.onend = rec.onerror = () => {
        voiceBtn.style.background = '';
        voiceBtn.style.color = '';
    };
}

document.getElementById('newChatBtn').addEventListener('click', createNewConv);
darkToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    const isDark = document.body.classList.contains('dark');
    localStorage.setItem('it_dark_mode', isDark ? '1' : '0');
    darkToggle.classList.toggle('active', isDark);
    darkToggle.textContent = isDark ? '☀' : '☾';
});
exportBtn.addEventListener('click', exportActiveConversation);
clearBtn.addEventListener('click', clearActiveConversation);
convSearch.addEventListener('input', e => renderConvList(e.target.value));
function openCtx(e, id) {
    e.stopPropagation();
    ctxTargetId = id;
    ctxMenu.style.left = Math.min(e.clientX, window.innerWidth - 170) + 'px';
    ctxMenu.style.top = Math.min(e.clientY, window.innerHeight - 90) + 'px';
    ctxMenu.classList.add('show');
}
function renameConv() {
    if (!ctxTargetId) return;
    const n = prompt('Renommer :', conversations[ctxTargetId]?.title || '');
    if (n !== null && n.trim()) {
        conversations[ctxTargetId].title = n.trim();
        save();
        renderConvList();
    }
    ctxMenu.classList.remove('show');
}
function deleteConv() {
    if (!ctxTargetId) return;
    if (!confirm('Supprimer cette conversation ?')) return;
    delete conversations[ctxTargetId];
    if (activeId === ctxTargetId) {
        activeId = null;
        renderMessages([]);
        showWelcome(true);
    }
    ctxTargetId = null;
    save();
    renderConvList();
    ctxMenu.classList.remove('show');
}
document.addEventListener('click', () => {
    ctxMenu.classList.remove('show');
    userDD.classList.remove('show');
});
document.getElementById('userMenuBtn').addEventListener('click', e => {
    e.stopPropagation();
    userDD.classList.toggle('show');
});
document.getElementById('hamburger').addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('show');
});
overlay.addEventListener('click', closeSidebar);
function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
}
function exportActiveConversation() {
    const c = conversations[activeId];
    if (!c || !c.messages?.length) {
        showToast('Aucune conversation à exporter', 'err');
        return;
    }
    const lines = [
        `AI IT Support - ${c.title}`,
        `Conversation: ${c.backendId || c.id}`,
        ''
    ];
    c.messages.forEach(m => {
        lines.push(`[${fmtTime(m.ts)}] ${m.role.toUpperCase()}: ${m.text || ''}`);
        if (m.ticketId || m.jiraTicketId) lines.push(`Ticket: ${m.jiraTicketId || m.ticketId}`);
        lines.push('');
    });
    const blob = new Blob([lines.join('\n')], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `conversation-${c.backendId || c.id}.txt`;
    a.click();
    URL.revokeObjectURL(url);
    showToast('Conversation exportée', 'ok');
}
function clearActiveConversation() {
    const c = conversations[activeId];
    if (!c || !confirm('Vider cette conversation localement ?')) return;
    c.messages = [];
    c.hasUrgent = false;
    c.hasEscalated = false;
    c.hasTicket = false;
    c.category = null;
    c.priority = null;
    c.updatedAt = Date.now();
    save();
    renderConvList();
    renderMessages([]);
}
function updateNetworkStatus() {
    const online = navigator.onLine;
    netPill.classList.toggle('off', !online);
    netPill.textContent = online ? '● Online' : '● Offline';
}
window.addEventListener('online', () => { updateNetworkStatus(); showToast('Connexion rétablie', 'ok'); });
window.addEventListener('offline', () => { updateNetworkStatus(); showToast('Vous êtes hors ligne', 'err'); });

(function init() {
    marked.setOptions({
        breaks: true,
        gfm: true,
        headerIds: false,
        mangle: false
    });
    if (localStorage.getItem('it_dark_mode') === '1') {
        document.body.classList.add('dark');
        darkToggle.classList.add('active');
        darkToggle.textContent = '☀';
    }
    load();
    updateNetworkStatus();
    renderConvList();
    renderFlagsBar();
    if (activeId && conversations[activeId]) {
        renderMessages(conversations[activeId].messages);
    } else {
        showWelcome(true);
    }
})();
</script>
</body>
</html>
