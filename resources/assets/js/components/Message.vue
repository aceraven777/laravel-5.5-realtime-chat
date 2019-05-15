<template>
    <li v-bind:id="'message-'+message.id" class="clearfix" :class="isMessageMine(message) ? 'right' : 'left'">
        <span class="chat-img" :class="isMessageMine(message) ? 'pull-right' : 'pull-left'">
            <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" />
        </span>
        
        <div class="chat-body clearfix">
            <div class="header" v-if="isMessageMine(message)">
                <small class="text-muted">
                    <span class="glyphicon glyphicon-time"></span>
                    <span data-toggle="tooltip" rel="tooltip" :title="formatFullDate()" v-text="ago"></span>
                </small>
                
                <strong class="pull-right primary-font" v-text="message.from_user.name"></strong>
            </div>

            <div class="header" v-else>
                <strong class="primary-font" v-text="message.from_user.name"></strong>

                <small class="pull-right text-muted">
                    <span class="glyphicon glyphicon-time"></span>
                    <span data-toggle="tooltip" rel="tooltip" :title="formatFullDate()" v-text="ago"></span>
                </small>
            </div>

            <p v-text="message.text"></p>
        </div>
    </li>
</template>

<script>
    import moment from 'moment';

    export default {
        props: ['message'],

        data() {
            return {
                currentUser: window.App.user,
                ago: '',
                dateRefreshTimer: false,
            };
        },

        created() {
            this.ago = this.formatDateAgo();

            clearInterval(this.dateRefreshTimer);
            this.dateRefreshTimer = setInterval(() => {
                this.ago = this.formatDateAgo();
            }, 60000);
        },

        methods: {
            isMessageMine(message) {
                return message.from_user_id == this.currentUser.id;
            },

            formatDateAgo() {
                return moment(String(this.message.created_at)).fromNow();
            },

            formatFullDate() {
                return moment(String(this.message.created_at)).format('MMM D, YYYY h:mma');
            },
        },
    }
</script>