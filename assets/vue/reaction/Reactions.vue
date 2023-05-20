<template>
  <ul class='list-unstyled d-flex flex-row'>
    <li class='p-2' @click="addReaction('hearts')">
      <font-awesome-icon :icon="['far', 'grin-hearts']" type='button' style="color: #669c35 ;" size='7x' />
    </li>
    <li class='p-2' @click="addReaction('stars')">
      <font-awesome-icon :icon="['far', 'grin-stars']" type='button' size="7x" style="color: #ffb43f;" />
    </li>
    <li class='p-2' @click="addReaction('tears')">
      <font-awesome-icon :icon="['far', 'grin-tears']" type='button' size='7x' style="color: #e32400;" />
    </li>
  </ul>
</template>

<script>
import Vue from "vue";
import ReceivedReaction from "./ReceivedReaction.vue";

export default {
  props: {sessionId: {type: String, required: true}},

  data() {
    return {
      stars: 0,
      hearts: 0,
      tears: 0,
      reactions: []
    }
  },

  created: function () {
    const RR = Vue.extend(ReceivedReaction);
    const vm = new RR;

    vm.$mount();
    document.body.appendChild(vm.$el);

    const session = `/api/session/${this.sessionId}`;

    fetch(`${session}/reaction`)
        .then(resp => resp.json().then(data => {
          let hubUrl = null;
          let linkHeader = resp.headers.get('Link');
          console.log( linkHeader )

          if (linkHeader) {
            let matchResult = linkHeader ? linkHeader.match(/<([^>]+)>;\s+rel="[^"]*mercure[^"]*"/) : null;
            if (matchResult) hubUrl = matchResult[1];
          }

          return { data, hubUrl };

        }))
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
  }
}

</script>


<style scoped>

</style>