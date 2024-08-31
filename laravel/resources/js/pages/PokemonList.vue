<template>
  <div class="p-6">
    <h1 class="text-3xl font-black">ポケモン一覧</h1>
    <div class="grid py-6 lg:px-8">
      <ul role="list" class="grid gap-x-8 gap-y-4 sm:grid-cols-3 xl:col-span-2">
        <li v-for="pokemon in pokemons" :key="pokemon.name">
          <div class="flex items-center">
            <img class="h-24 w-24 rounded-full" :src="pokemon.imageUrl" />
            <span class="text-base font-semibold text-gray-900 ml-4">
              {{ pokemon.name }}
            </span>
            <template v-if="isIncludeFavorites(pokemon.englishName)">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-yellow-500 ml-3"
                viewBox="0 0 24 24"
                fill="currentColor"
                stroke="none"
                @click="deleteFavorite(pokemon)"
              >
                <path
                  d="M12 17.27L18.18 21l-1.45-6.36L22 9.27l-6.36-.55L12 2 8.36 8.73 2 9.27l4.27 5.37L4.82 21z"
                />
              </svg>
            </template>
            <template v-else>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-8 w-8 text-gray-300 ml-3"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                @click="addFavorite(pokemon)"
              >
                <path
                  d="M12 17.27L18.18 21l-1.45-6.36L22 9.27l-6.36-.55L12 2 8.36 8.73 2 9.27l4.27 5.37L4.82 21z"
                />
              </svg>
            </template>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <div
    class="fixed top-8 right-8 border border-black bg-white rounded shadow-lg py-4 px-6"
    :class="{ hidden: isModalHidden }"
  >
    <p>{{ modalMessage }}</p>
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
      favoriteNames: [],
      isModalHidden: true,
      modalMessage: "",
      modalTimeout: null,
    };
  },

  async created() {
    await this.fetchPokemonList();
    await this.fetchFavorites();
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
        this.pokemons.push({ name, imageUrl, englishName: result.name });
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

    /**
     * お気に入り取得
     */
    async fetchFavorites() {
      const { data } = await axios.get("/api/pokemons/favorites");
      this.favoriteNames = data;
    },

    /**
     * お気に入り登録／解除メッセージ表示
     */
    showModal(message) {
      this.modalMessage = message;
      this.isModalHidden = false;
      if (this.modalTimeout !== null) {
        clearTimeout(this.modalTimeout);
      }
      this.modalTimeout = setTimeout(() => {
        this.isModalHidden = true;
      }, 3000);
    },

    /**
     * お気に入り登録されているか
     */
    isIncludeFavorites(englishName) {
      return this.favoriteNames.some(
        (favoriteName) => favoriteName === englishName
      );
    },

    /**
     * お気に入り登録
     */
    async addFavorite(addPokemon) {
      const { status } = await axios.post("/api/pokemons/add-favorite", {
        pokemon_english_name: addPokemon.englishName,
      });
      if (status === 200) {
        this.favoriteNames.push(addPokemon.englishName);
        this.showModal(`${addPokemon.name}をお気に入り登録しました。`);
      }
    },

    /**
     * お気に入り解除
     */
    async deleteFavorite(deletePokemon) {
      const { status } = await axios.post("/api/pokemons/delete-favorite", {
        pokemon_english_name: deletePokemon.englishName,
      });
      if (status === 200) {
        this.favoriteNames = this.favoriteNames.filter(
          (favoriteName) => favoriteName !== deletePokemon.englishName
        );
        this.showModal(`${deletePokemon.name}をお気に入り解除しました。`);
      }
    },
  },
};
</script>
