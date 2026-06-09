<template>
    <ul class="chat">
        <li v-for="(message, index) in messages" :key="message.user.id + message.created_at" class="clearfix">
            <div v-if="isNewDate(message.created_at, index)">
                <div class="date-header">{{ formatDate(message.created_at) }}</div>
            </div>
            <div :class="['message-container', message.user.role === 'administrator' ? 'right' : 'left']">
                <div :class="['message', message.user.role === 'administrator' ? 'admin' : 'user']">
                    <p>{{ message.message }}</p>
                    <div v-if="message.attachments && message.attachments.length > 0" class="attachments">
                        <div v-for="(attachment, index) in message.attachments" :key="index">
                            <a v-if="isImage(attachment.type)" @click.prevent="openImage(attachment.path)">
                                <img :src="attachment.path" alt="Image attachment" class="attachment-image" />
                            </a>
                            <a v-else :href="attachment.path" target="_blank" class="attachment-file">
                                {{ attachment.name || 'Вложение' }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="header" :class="{ right: message.user.role === 'administrator', left: message.user.role !== 'administrator' }">
                    <strong>{{ message.user.role === 'administrator' ? 'Администратор' : message.user.id }}</strong>
                    <span v-if="message.created_at"> в {{ formatTime(message.created_at) }}</span>
                </div>
            </div>
        </li>
    </ul>

    <div v-if="showModal" class="modal" @click="closeModal">
        <img :src="modalImageUrl" alt="Opened attachment" class="modal-image" />
    </div>
</template>

<script>

import { ref } from "vue";

export default {
    props: {
        messages: {
            type: Array,
            required: true
        }
    },
    setup(props) {
        const formatTime = (date) => {
            const options = { hour: '2-digit', minute: '2-digit' };
            return new Date(date).toLocaleTimeString([], options);
        };

        const isNewDate = (currentDate, index) => {
            if (index === 0) return true;

            const previousDate = new Date(props.messages[index - 1].created_at);
            const currentMsgDate = new Date(currentDate);
            return (
                currentMsgDate.getDate() !== previousDate.getDate() ||
                currentMsgDate.getMonth() !== previousDate.getMonth() ||
                currentMsgDate.getFullYear() !== previousDate.getFullYear()
            );
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString('ru-RU', {
                month: 'long',
                day: 'numeric'
            });
        };

        const isImage = (mimeType) => {
            return mimeType.startsWith('image/');
        }

        const showModal = ref(false);
        const modalImageUrl = ref('');

        const openImage = (url) => {
            modalImageUrl.value = url;
            showModal.value = true;
        };

        const closeModal = () => {
            showModal.value = false;
            modalImageUrl.value = '';
        };

        return {
            formatTime,
            isNewDate,
            formatDate,
            isImage,
            showModal,
            modalImageUrl,
            openImage,
            closeModal,
        };
    }
}
</script>

<style scoped>
.chat {
    list-style-type: none;
    padding: 0;
    margin: 0;
    overflow-y: scroll;
    flex-grow: 1;
}

.date-header {
    font-size: 1.1em;
    margin: 10px 0;
    text-align: center;
    color: #aaa;
}

.message {
    display: inline-block;
    padding: 10px;
    border-radius: 10px;
    word-wrap: break-word;
    max-width: 100%;
}

.message-container {
    margin-bottom: 5px;
    padding: 10px;
    max-width: 80%;
}

.admin {
    background-color: #6A4CA5;
    color: white;
    border-radius: 10px;
    padding: 10px;
    margin-left: auto;
}

.user {
    background-color: #282B34;
    color: white;
    border-radius: 10px;
    padding: 10px;
    margin-right: auto;
}

.left {
    text-align: left;
    margin-right: auto;
}

.right {
    text-align: right;
    margin-left: auto;
}

.header {
    margin-top: 5px;
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #aaa;
    text-align: left;
}

.header.right {
    text-align: right;
}

.header strong {
    color: white;
}

.attachments {
    margin-top: 5px;
}

.attachment-image {
    max-width: 100px;
    max-height: 100px;
    margin: 5px;
    border-radius: 5px;
}

.attachment-file {
    display: block;
    color: #007bff;
    margin: 5px 0;
    text-decoration: none;
}

.attachment-file:hover {
    text-decoration: underline;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-image {
    max-width: 90%;
    max-height: 90%;
    border-radius: 8px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.5);
}
</style>
