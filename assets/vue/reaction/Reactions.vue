<template>
  <ul class='list-unstyled d-flex flex-row'>
    <li class='p-2' @click="addReaction('hearts')">
      <font-awesome-icon :icon="['far', 'grin-hearts']" type='button' style="color: #669c35 ;" size='7x' />
    </li>
    <li class='p-2' @click="addReaction('stars')">
      <font-awesome-icon :icon="['far', 'grin-stars']" size="7x" style="color: #ffb43f;" />
    </li>
    <li class='p-2' @click="addReaction('tears')">
      <font-awesome-icon :icon="['far', 'grin-tears']" size='7x' style="color: #e32400;" />
    </li>
  </ul>
</template>

<script>
import Vue from "vue";
import ReceivedReaction from "./ReceivedReaction.vue";

export default {
  created: function () {
    const RR = Vue.extend(ReceivedReaction);
    const vm = new RR;
    vm.$mount();
    document.body.appendChild(vm.$el);

    const session = `/api/session/${this.sessionId}`;

    fetch(`${session}/reaction`)
        .then(resp => resp.json().then(data => ({
          data,
          hubUrl: resp.headers.get('Link').match(/<([^>]+)>;\s+rel="[^"]*mercure[^"]*"/)[1]
        })))
        .then(({data, hubUrl}) => {

          Object.entries(data).forEach(([type, nb]) => this[type] = nb);

          const es = new EventSource(`${hubUrl}?topic=${document.location.origin}/api/reactions/{id}`);

          es.onmessage = ({data}) => {

            const reaction = JSON.parse(data);
            if (reaction.session !== session) return;

            ++this[reaction.type];
            vm.displayReceivedReaction(reaction.type)
          }
        })
  },
  data() {
    return {
      stars: 0,
      hearts: 0,
      tears: 0,
      reactions: []
    }
  },

  methods: {
    addReaction(type) {
      fetch('/api/reactions', {
        method: 'POST',
        headers: {'Content-Type': 'application/ld+json'},
        body: JSON.stringify({session: `/api/sessions/${this.sessionId}`, type})
      })
          .then(({ok, statusText}) => {
            if (!ok) alert(statusText)
          })
    }
  },

  props: {sessionId: {type: String, required: true}}

}

</script>


<style scoped>

</style>