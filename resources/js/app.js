import './bootstrap'
//import './echo.js';
import {createApp, defineAsyncComponent, ref} from 'vue';

const app = createApp({
    setup() {
        const messages = ref(window.initialChatMessages || []);

        // const channel = Echo.private(`chats.${chatId}.messages`);
        // channel.listen('.new-message', function(e) {
        //     messages.value.push({
        //         message: e.message.message,
        //         user: {
        //             id: e.message.user.id,
        //             role: e.message.user.role,
        //         },
        //         attachments: e.message.attachments || [],
        //         created_at: e.message.created_at
        //     });
        // });

        const addMessage = (message) => {
            messages.value.push(message);
        };

        return {
            messages,
            addMessage,
            initialChatMessages: window.initialChatMessages || []
        };
    },
});

// const notificationsApp = createApp({
//     setup() {
//         const notifications = Echo.private(`admin-notifications`);
//         notifications.notification((notification) => {
//             dispatchEvent(
//                 new CustomEvent('toast', {
//                     detail: {
//                         type: 'primary',
//                         text: notification.message,
//                     },
//                 })
//             );
//         });
//     },
// });

// notificationsApp.mount('#notifications-app');


const ChatMessages = defineAsyncComponent(() =>
    import('./components/ChatMessages.vue')
);
const ChatForm = defineAsyncComponent(() =>
    import('./components/ChatForm.vue')
);

app.component('chat-messages', ChatMessages);
app.component('chat-form', ChatForm);

app.mount('#app');
