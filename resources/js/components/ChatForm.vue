<template>
    <div class="input-container">
        <input
            id="btn-input"
            type="text"
            name="message"
            class="message-input"
            placeholder="Написать"
            v-model="newMessage"
            @keyup.enter="sendMessage"
        />
        <button class="send-button" id="btn-chat" @click="sendMessage">
            Отправить
        </button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ["user"],
    data() {
        return {
            newMessage: "",
        };
    },
    methods: {
        async sendMessage() {
            if (!this.newMessage.trim()) {
                return;
            }

            try {
                const response = await axios.post(
                    `/bank/message`,
                    { message: this.newMessage }
                );

            this.newMessage = "";
        } catch (error) {
            console.error("Ошибка при отправке сообщения:", error);
            }
        },
    },
};
</script>

<style scoped>
.input-container {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #FC8EAC;
    border-top: 1px solid #FFB6C1;
    border-radius: 5px;
}

.message-input {
    flex-grow: 1;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
    background-color: #FFC0CB;
    color: deeppink;
    margin-right: 10px;
}

.message-input::placeholder {
    color: #72767d;
}

.send-button {
    padding: 10px 20px;
    background-color: #E30B5C;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.send-button:hover {
    background-color: #E30B5C;
}
</style>

