<template>
    <div class="panel panel-default">
        <div class="panel-heading" v-text="toUser.name"></div>

            <div class="panel-body">
                <div class='chat-messages'>
                    <div v-for="message in messages" v-bind:id="'message-'+message.id" class='single-message'>
                        <h5 v-text="message.from_user.name"></h5>
                        <p v-text="message.text"></p>
                    </div>
                </div>

                <form id="chat-user-form" @submit.prevent="chat" class="form-horizontal" autocomplete="off">
                    <div class="text-input form-inline">
                        <input type="text" name="text" v-model="text" class="form-control" placeholder="Type your message here..." required />
                        <button type="submit" class="btn btn-primary" :class="isSubmitting ? 'disabled' : ''">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['toUser'],

        data() {
            return {
                currentUser: window.App.user,
                messages: [],
                text: '',
                isSubmitting: false,
                isInitialRender: false,
            };
        },

        created() {
            axios.get("/api/messages/" + this.toUser.id)
                .then((response) => {
                    for (var i = 0; i < response.data.length; i++) {
                       this.messages.unshift(response.data[i]); 
                    }

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
                this.$el.querySelector("#chat-user-form").scrollIntoView();
                this.isInitialRender = false;
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
                        console.log('11');
                        console.log(response.data.status);
                        console.log(response.data);

                        if (! response.data.status) {
                            alertify.error("Can't send message! Please try again.", 10);
                            return;
                        }

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
                let text = message.text;
                let user = (fromUserId == this.currentUser.id ? this.currentUser : this.toUser);

                this.messages.push({
                    id: message.id,
                    from_user: user,
                    text: text,
                });
            },

            isMessageExisting(message) {
                for(var i = this.messages.length - 1; i >= 0; i--) {
                    if (this.messages[i].id == message.id) {
                        return true;
                    }
                }

                return false;
            },
        },
    }
</script>