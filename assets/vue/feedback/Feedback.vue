<template>
  <div class='border-dark border-3 border-top'>
    <h3 class='my-5'>Commentaires</h3>
    <ul v-if="feedback.length" class="list-unstyled">
      <li class='border-secondary border-1 border-bottom border-opacity-25 my-3' v-for='f in feedback' :key="f['@id']">
        <div class='d-flex'>
          <div class='flex-grow-1'>
            <p class='fw-bold my-0'>{{f.author}}</p>
            <p>{{f.comment}}</p>
          </div>
          <div class=''>
            <start-rating :rating='f.rating' :star-size='20' :read-only='true' inactive-color="#dfe4ea" active-color="#fff200"></start-rating>
          </div>
        </div>
      </li>
    </ul>
    <p v-else>Pas de feedback pour le moment</p>

    <p v-if="sent">Merci pour votre vote!!!</p>
    <form v-else @submit.prevent='onSubmit' class='w-75 mx-auto my-5 py-5'>
      <h3 class='py-4'>Votre Commentaire</h3>
      <div class='form-floating'>
        <input id='form-author' class='form-control' v-model='author' name='author' placeholder='Auteur' value='Votre nom'>
        <label for='form-author' class='form-control bg-transparent'>Auteur</label>
      </div>

      <start-rating class='my-4' v-model='rating' :star-size="30" inactive-color="#dfe4ea" active-color="#fff200"></start-rating>
      <div class='form-floating'>
        <textarea id='form-comment' class='form-control' style="height: 150px" v-model="comment" name='comment' placeholder='Votre message'></textarea>
        <label for='form-comment' class='form-control bg-transparent'>Votre message</label>
      </div>
      <input class='btn btn-outline-primary my-3' :disabled='!author || !rating || !comment' type='submit' value='Valider'>
    </form>
  </div>
</template>

<script>
export default {
  props: ['sessionId'],
  data(){
    return{feedback: [], author:'', rating: 0,comment: '', sent: false}
  },
  created() {
    this.fetchFeedback();
  },
  methods: {
    fetchFeedback() {
      fetch(`/api/session/${this.sessionId}/feedback`)
          .then(resp => resp.json())
          .then(data => this.feedback = data['hydra:member'])
    },
    onSubmit(){
      const {sessionId, author, rating, comment} = this;
      fetch('/api/feedback', {
        method: 'POST',
        headers: {'Content-type': 'application/ld+json'},
        body: JSON.stringify({
          session: `/api/sessions/${this.sessionId}`,
          author,
          rating,
          comment
        })
      }).then(() => {
        this.sent = true;
        this.fetchFeedback();
      })
    }

  }
}
</script>

<style scoped>

</style>