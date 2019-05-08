<template>
    <div class="panel panel-default">
        <div class="panel-heading"><h4 v-text="toUser.name"></h4></div>

        <div class="panel-body">
            <ul class="chat">
                <li v-for="message in messages" v-bind:id="'message-'+message.id" class="clearfix" :class="isMessageMine(message) ? 'right' : 'left'">
                    <span class="chat-img" :class="isMessageMine(message) ? 'pull-right' : 'pull-left'">
                        <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" />
                    </span>
                    
                    <div class="chat-body clearfix">
                        <div class="header" v-if="isMessageMine(message)">
                            <small class="text-muted">
                                <span class="glyphicon glyphicon-time"></span>
                                <span v-text="formatDate(message.created_at)"></span>
                            </small>
                            
                            <strong class="pull-right primary-font" v-text="message.from_user.name"></strong>
                        </div>

                        <div class="header" v-else>
                            <strong class="primary-font" v-text="message.from_user.name"></strong>

                            <small class="pull-right text-muted">
                                <span class="glyphicon glyphicon-time"></span>
                                <span v-text="formatDate(message.created_at)"></span>
                            </small>
                        </div>

                        <p v-text="message.text"></p>
                    </div>
                </li>
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
    import moment from 'moment';

    export default {
        props: ['toUser'],

        data() {
            return {
                currentUser: window.App.user,
                messages: [],
                text: '',
                isSubmitting: false,
                isInitialRender: false,
                isChatInserted: false,
            };
        },

        created() {
            axios.get("/api/messages/" + this.toUser.id)
                .then((response) => {
                    for (var i = 0; i < response.data.length; i++) {
                       this.messages.unshift(response.data[i]); 
                    }

                    console.log(this.messages);

                    this.isInitialRender = true;
                });

            Echo.private('App.Message.' + this.currentUser.id + '.' + this.toUser.id)
                .listen('ChatSent', (e) => {
                    var message = e.message;
                    
                    this.insertChat(message);
                });

            Echo.private('App.Message.' + this.toUser.id + '.' + this.currentUser.id)
                .listen('ChatSent', (e) => {
                    var message = e.message;
                    
                    this.insertChat(message);
                });
        },

        updated: function () {
            if (this.isInitialRender) {
                this.scrollChatToBottom();
                this.isInitialRender = false;
            }
            else if (this.isChatInserted) {
                this.scrollChatToBottom();
                this.isChatInserted = false;
            }
        },

        methods: {
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

            scrollChatToBottom() {
                var panel_body = this.$el.querySelector(".panel-body");
                
                panel_body.scrollTop = panel_body.scrollHeight;
            },

            isMessageExisting(message) {
                for (var i = this.messages.length - 1; i >= 0; i--) {
                    if (this.messages[i].id == message.id) {
                        return true;
                    }
                }

                return false;
            },

            isMessageMine(message) {
                return message.from_user_id == this.currentUser.id;
            },

            formatDate(date) {
                console.log('mind');
                return moment(String(date)).format('MMM D, YYYY h:mma');
            },
        },
    }
</script>