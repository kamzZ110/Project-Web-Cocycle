<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - CoCycle Partner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Backed -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --green-sage: #8A9A5B;
            --gold: #F6C55A;
            --bg-light: #f8f9f4;
            --bubble-me: #8A9A5B;
            --bubble-them: #ffffff;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Poppins', sans-serif; 
            height: 100vh;
            overflow: hidden; 
        }

        .top-bar {
            background: #fff;
            padding: 10px 25px;
            border-bottom: 2px solid var(--gold);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
        }

        .chat-wrapper {
            height: calc(100vh - 60px);
            display: flex;
        }

        .chat-sidebar {
            width: 350px;
            background: #fff;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
        }

        .search-box { padding: 15px; border-bottom: 1px solid #f5f5f5; }
        .search-box input {
            background: #f1f1f1;
            border: none;
            border-radius: 10px;
            padding: 8px 15px;
            font-size: 0.9rem;
            width: 100%;
        }

        .chat-list { overflow-y: auto; flex-grow: 1; }
        .chat-item {
            padding: 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #f9f9f9;
            transition: 0.2s;
        }
        .chat-item:hover, .chat-item.active { background-color: #f4f7f1; }
        
        .avatar {
            width: 45px; height: 45px;
            background: #ddd; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-right: 12px; font-weight: bold; color: #777;
            flex-shrink: 0; overflow: hidden;
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        .chat-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: #f4f7f1;
        }

        .chat-header {
            padding: 10px 20px;
            background: #fff;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            height: 60px;
        }

        .message-area {
            flex-grow: 1;
            padding: 25px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }
        
        .bubble.them {
            align-self: flex-start;
            background: var(--bubble-them);
            border-bottom-left-radius: 2px;
            color: #444;
        }

        .bubble.me {
            align-self: flex-end;
            background: var(--bubble-me);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .msg-time {
            font-size: 0.7rem;
            display: block;
            margin-top: 5px;
            opacity: 0.7;
        }

        .chat-input-area {
            padding: 15px 25px;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .input-group-custom {
            flex-grow: 1;
            background: #f1f1f1;
            border-radius: 25px;
            padding: 5px 15px;
            display: flex;
            align-items: center;
        }
        .input-group-custom input {
            border: none; background: transparent; width: 100%; padding: 8px; outline: none;
        }

        .btn-send {
            background: var(--green-sage);
            color: white;
            border: none;
            width: 42px; height: 42px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s;
        }
        .btn-send:hover { background: #7a8a4f; transform: scale(1.05); }
    @media(max-width: 600px) { .chat-sidebar {width:35%;min-width:125px} .chat-main {min-width:0} .chat-item{padding:10px 5px}.chat-item .avatar{display:none}.message-area{padding:12px}.bubble{max-width:95%;overflow-wrap:anywhere}.chat-input-area{padding:10px;gap:5px}.input-group-custom{min-width:0}.search-box{padding:8px}.search-box input{padding:8px;font-size:12px} }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="d-flex align-items-center">
            <a href="{{ route(Auth::user()->role === 'partner' ? 'partner.home' : 'customer.home') }}" class="text-dark me-3"><i class="fas fa-arrow-left fs-4"></i></a>
            <span class="fw-bold text-success fs-5">PESAN</span>
        </div>
        <div class="fw-bold small">{{ $store?->store_name ?? Auth::user()->username }}</div>
    </div>

    <div class="chat-wrapper">
        <!-- SIDEBAR -->
        <div class="chat-sidebar">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari kontak...">
            </div>
            <div class="chat-list" id="contactList">
                <div class="text-center mt-5 text-muted small">
                    <div class="spinner-border spinner-border-sm mb-2" role="status"></div><br>
                    Memuat percakapan...
                </div>
            </div>
        </div>

        <!-- MAIN CHAT -->
        <div class="chat-main">
            <!-- HEADER -->
            <div class="chat-header d-none" id="chatHeader">
                <div class="avatar" id="headerAvatar">?</div>
                <div class="ms-3">
                    <h6 class="mb-0 fw-bold" id="headerName">Nama User</h6>
                    
                </div>
            </div>

            <!-- EMPTY STATE -->
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-muted" id="emptyState">
                <i class="fas fa-comments fs-1 mb-3" style="opacity: 0.3;"></i>
                <p>Pilih percakapan untuk memulai chat.</p>
            </div>

            <!-- MESSAGE AREA -->
            <div class="message-area d-none" id="messageArea"></div>

            <!-- INPUT AREA -->
            <div class="chat-input-area d-none" id="inputArea">
                <div class="input-group-custom">
                    <input type="text" id="msgInput" placeholder="Tulis pesan anda..." autocomplete="off">
                </div>
                <button class="btn-send" id="btnSend">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-5.3.0.bundle.min.js') }}"></script>
    <script>
        const chatBase = @json(url(Auth::user()->role === 'partner' ? '/partner/chat' : '/chat'));
        let currentContactId = null;
        let lastMessages = '';
        function loadContacts() {
            const q = $('#searchInput').val().trim();
            $.get(chatBase + (q ? '/search' : '/contacts'), {q}, function(data) {
                const list = $('#contactList').empty();
                if (!data.length) list.append($('<p class="p-3 text-muted small">').text(q ? 'Kontak tidak ditemukan.' : 'Belum ada riwayat chat.'));
                data.forEach(contact => {
                    const item = $('<div class="chat-item">').toggleClass('active', contact.id === currentContactId);
                    $('<div class="avatar">').append($('<img alt="">').attr('src', contact.profile_pic)).appendTo(item);
                    const content = $('<div class="flex-grow-1" style="min-width:0">').appendTo(item);
                    $('<h6 class="fw-bold text-truncate">').text(contact.fullname || contact.username).appendTo(content);
                    $('<small class="d-block text-muted text-truncate">').text(contact.last_message).appendTo(content);
                    item.on('click', () => selectContact(contact));
                    list.append(item);
                });
            }).fail(() => $('#contactList').text('Tidak dapat memuat kontak.'));
        }
        function selectContact(contact) {
            currentContactId = contact.id;
            lastMessages = '';
            $('#emptyState').addClass('d-none');
            $('#chatHeader, #messageArea, #inputArea').removeClass('d-none');
            $('#headerName').text(contact.fullname || contact.username);
            $('#headerAvatar').empty().append($('<img alt="">').attr('src', contact.profile_pic));
            loadMessages();
        }
        function loadMessages() {
            if (!currentContactId) return;
            const selected = currentContactId;
            $.get(chatBase + '/messages', {contact_id: selected}, function(data) {
                if (selected !== currentContactId || JSON.stringify(data) === lastMessages) return;
                lastMessages = JSON.stringify(data);
                const area = $('#messageArea').empty();
                data.forEach(message => {
                    const bubble = $('<div class="bubble">').addClass(message.is_me ? 'me' : 'them').text(message.message);
                    $('<span class="msg-time">').text(message.time).appendTo(bubble);
                    area.append(bubble);
                });
                area.scrollTop(area[0].scrollHeight);
            });
        }
        function sendMessage() {
            const message = $('#msgInput').val().trim();
            if (!message || !currentContactId || $('#btnSend').prop('disabled')) return;
            $('#btnSend').prop('disabled', true);
            $.post(chatBase + '/send', {_token: @json(csrf_token()), receiver_id: currentContactId, message})
                .done(() => { $('#msgInput').val(''); loadMessages(); loadContacts(); })
                .fail(xhr => alert(xhr.responseJSON?.message || 'Gagal mengirim pesan.'))
                .always(() => $('#btnSend').prop('disabled', false));
        }
        $('#btnSend').on('click', sendMessage);
        $('#msgInput').on('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); sendMessage(); } });
        let searchTimer;
        $('#searchInput').on('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(loadContacts, 250); });
        loadContacts();
        setInterval(loadMessages, 3000);
        setInterval(() => { if (!$('#searchInput').val()) loadContacts(); }, 5000);
    </script>
</body>
</html>
