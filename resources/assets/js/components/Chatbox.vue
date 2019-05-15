<template>
    <div class="panel panel-default">
        <div class="panel-heading"><h4 v-text="toUser.name"></h4></div>

        <div class="panel-body">
            <ul class="chat">
                <Message v-for="message in messages" v-bind:key="message.id" :message="message" />
            </ul>
        </div>

        <div class="panel-footer">
            <form id="chat-user-form" @submit.prevent="chat" class="form-horizontal" autocomplete="off">
                <div class="input-group">
                    <input type="text" name="text" v-model="text" class="form-control input-md" placeholder="Type your message here..." required>
                    
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-md" :class="isSubmitting ? 'disabled' : ''">
                            Send
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Message from './Message';

    export default {
        components: {Message},

        props: ['toUser'],

        data() {
            return {
                currentUser: window.App.user,
                messages: [],
                text: '',
                isFetchingChats: false,
                isSubmitting: false,
                isInitialRender: false,
                isChatInserted: false,
                isChatsFetched: false,
                scrollToMessageId: 0,
                hasMoreMessages: true,
            };
        },

        created() {
            this.getChatMessages(() => {
                this.isInitialRender = true;
            });

            Echo.private('App.Message.' + this.currentUser.id + '.' + this.toUser.id)
                .listen('ChatSent', (e) => {
                    let message = e.message;
                    
                    this.isChatInserted = true;
                    this.insertChat(message);
                });

            Echo.private('App.Message.' + this.toUser.id + '.' + this.currentUser.id)
                .listen('ChatSent', (e) => {
                    let message = e.message;
                    
                    this.isChatInserted = true;
                    this.insertChat(message);
                });
            
            $(this.$el).tooltip({
                selector: '[rel=tooltip]'
            });
        },

        updated: function () {
            if (this.isInitialRender) {
                this.scrollChatToBottom();
                this.isInitialRender = false;
                this.isChatsFetched = false;

                let panel_body = this.$el.querySelector(".panel-body");
                panel_body.addEventListener("scroll", () => {
                    if (this.isScrollAtTop()) {
                        this.getChatMessages();
                    }
                });
            }
            else if (this.isChatsFetched) {
                this.isChatsFetched = false;
                document.getElementById('message-' + this.scrollToMessageId).scrollIntoView();
            }
            else if (this.isChatInserted) {
                this.scrollChatToBottom();
                this.isChatInserted = false;
            }
        },

        methods: {
            getChatMessages(callback) {
                if (! this.hasMoreMessages || this.isFetchingChats) {
                    return;
                }

                var last_message_id = (this.messages.length ? this.messages[0].id  : null);
                
                this.isFetchingChats = true;

                axios.get("/api/messages/" + this.toUser.id, {
                    params: {
                        last_message_id: last_message_id
                    }
                })
                .then((response) => {
                    for (let i = 0; i < response.data.messages.length; i++) {
                        this.messages.unshift(response.data.messages[i]); 
                    }

                    if (last_message_id) {
                        this.scrollToMessageId = last_message_id;
                    }

                    this.isChatsFetched = true;

                    if (typeof callback == 'function') {
                        callback();
                    }

                    this.hasMoreMessages = response.data.has_more_messages;
                })
                .catch((error) => {
                })
                .then(() => {
                    this.isFetchingChats = false;
                });
            },

            chat() {
                if (this.isSubmitting) {
                    return;
                }

                this.isSubmitting = true;

                axios.post("/api/users/" + this.toUser.id + "/messages", {text: this.text})
                    .then((response) => {
                        // handle success
                        if (! response.data.status) {
                            alertify.error("Can't send message! Please try again.", 10);
                            return;
                        }

                        this.isChatInserted = true;
                        this.insertChat(response.data.message);
                        this.text = "";
                    })
                    .catch((error) => {
                        alertify.error("Can't send message! Please try again.", 10);
                    })
                    .then(() => {
                        this.isSubmitting = false;
                    });
            },

            insertChat(message) {
                if (this.isMessageExisting(message)) {
                    return;
                }

                let fromUserId = message.from_user_id;
                let user = (fromUserId == this.currentUser.id ? this.currentUser : this.toUser);

                this.messages.push({
                    id: message.id,
                    from_user: user,
                    from_user_id: fromUserId,
                    text: message.text,
                    created_at: message.created_at,
                });
            },

            isScrollAtTop() {
                let panel_body = this.$el.querySelector(".panel-body");
                
                return panel_body.scrollTop == 0;
            },

            scrollChatToBottom() {
                let panel_body = this.$el.querySelector(".panel-body");
                
                panel_body.scrollTop = panel_body.scrollHeight;
            },

            isMessageExisting(message) {
                for (let i = this.messages.length - 1; i >= 0; i--) {
                    if (this.messages[i].id == message.id) {
                        return true;
                    }
                }

                return false;
            },
        },
    }
</script>