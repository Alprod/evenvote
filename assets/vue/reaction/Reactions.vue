<template>
  <ul class='list-unstyled d-flex flex-row'>
    <li class='p-2' @click="addReaction('hearts')">
      <font-awesome-icon :icon="['far', 'face-grin-hearts']" :class="['face-hearts']" type='button' size='7x'/>
    </li>
    <li class='p-2' @click="addReaction('stars')">
      <font-awesome-icon :icon="['far', 'face-grin-stars']" :class="['face-stars']" type='button' size='7x'/>
    </li>
    <li class='p-2' @click="addReaction('tears')">
      <font-awesome-icon :icon="['far', 'face-grin-tears']" :class="['face-tears']" type='button' size='7x'/>
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

  created() {
    const RR = Vue.extend(ReceivedReaction);
    const vm = new RR;

    vm.$mount();
    document.body.appendChild(vm.$el);

    const session = `/api/session/${this.sessionId}`;

    fetch(`${session}/reactions`)
        .then(resp => resp.json().then(data => {
          let hubUrl = null;
          let linkHeader = resp.headers.get('Link');
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
            const s1 = reaction.session.split('/').slice(-1)
            const s2 = session.split('/').slice(-1)

            let react = parseInt(s1[0])
            let sess = parseInt(s2[0])

            if(react !== sess) return;

            vm.displayReceivedReaction(reaction.type);
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