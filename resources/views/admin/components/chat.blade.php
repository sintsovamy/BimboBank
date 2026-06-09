<div id="app">
    <div class="container" style="display: flex;">
        <div class="chat-container" style="flex: 2; margin-right: 20px;">
            <div class="card" style="background-color: #FFF4FD;">
                <div class="card-body">
                    <chat-messages :messages="initialChatMessages"></chat-messages>
                </div>
                <div class="card-footer" style="background-color: #FFF4FD;">
                    <chat-form
                        v-on:messagesent="addMessage"
                        :user="{{ json_encode(auth('moonshine')->user()) }}">
                    </chat-form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .extra-content p {
        margin-bottom: 20px;
    }

    .card-body {
        height: 500px;
        overflow-y: auto;
        padding: 15px;
        background-color: #FFC0CB;
        border-radius: 5px;
        border: none;
    }
</style>

<script>
    window.initialChatMessages = @json($messages);
</script>
@vite(['resources/js/app.js', 'resources/css/app.css'])

