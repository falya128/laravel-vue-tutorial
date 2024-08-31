<template>
  <div class="p-6">
    <h1 class="text-3xl font-black">ポケモン一覧</h1>
    <div class="grid py-6 lg:px-8">
      <ul role="list" class="grid gap-x-8 gap-y-4 sm:grid-cols-3 xl:col-span-2">
        <li v-for="pokemon in pokemons" :key="pokemon.name">
          <div class="flex items-center gap-x-6">
            <img class="h-24 w-24 rounded-full" :src="pokemon.imageUrl" />
            <h3 class="text-base font-semibold text-gray-900">
              {{ pokemon.name }}
            </h3>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const DEFAULT_OFFSET = 0;
const DEFAULT_LIMIT = 20;

export default {
  data() {
    return {
      pokemons: [],
    };
  },

  async created() {
    await this.fetchPokemonList();
  },

  computed: {
    offset() {
      return Number(this.$route.query.offset || DEFAULT_OFFSET);
    },
    limit() {
      return Number(this.$route.query.limit || DEFAULT_LIMIT);
    },
  },

  methods: {
    /**
     * 一覧表示するポケモンのリストを取得
     */
    async fetchPokemonList() {
      const { data } = await axios.get("https://pokeapi.co/api/v2/pokemon", {
        params: { offset: this.offset, limit: this.limit },
      });

      this.pokemons = [];
      data.results.forEach(async (result) => {
        const imageUrl = await this.fetchPokemonImage(result.name);
        const name = await this.fetchPokemonName(result.name);
        this.pokemons.push({ name, imageUrl });
      });
    },

    /**
     * ポケモンの画像を取得
     */
    async fetchPokemonImage(name) {
      const url = `https://pokeapi.co/api/v2/pokemon/${name}`;
      const { data } = await axios.get(url);
      return data?.sprites?.other?.["official-artwork"]?.front_default;
    },

    /**
     * ポケモンの名前を取得
     */
    async fetchPokemonName(name) {
      const url = `https://pokeapi.co/api/v2/pokemon-species/${name}`;
      const { data } = await axios.get(url);
      return data.names.find((name) => name.language.name === "ja").name;
    },
  },
};
</script>
