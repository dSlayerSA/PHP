<template>
  <v-app>
    <v-app-bar app color="indigo darken-2" dark>
      <v-btn icon @click="drawer = !drawer">
        <v-icon>mdi-menu</v-icon>
      </v-btn>
      <v-toolbar-title>My App</v-toolbar-title>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app>
      <v-list>
        <v-list-item @click="selectPage('alunos')">
          <v-list-item-title>Alunos</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <v-container>
        <v-card v-if="selectedPage === 'alunos'">
          <v-card-title>Pesquisar Alunos
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="openAddAlunoDialog">Adicionar Aluno</v-btn>
          </v-card-title>

          <v-card-text>
            <v-text-field v-model="searchText" label="Pesquise por RA ou CPF do aluno" outlined></v-text-field>
            <v-btn @click="show" color="primary">Pesquisar</v-btn>
          </v-card-text>

          <v-data-table v-if="alunos.length > 0" :headers="headers" :items="alunos">
            <template v-slot:RA="{ item }">
              <v-text-field v-model="item.RA" dense outlined hide-details></v-text-field>
            </template>
            <template v-slot:nome="{ item }">
              <v-text-field v-model="item.nome" dense outlined hide-details></v-text-field>
            </template>
            <template v-slot:cpf="{ item }">
              <v-text-field v-model="item.cpf" dense outlined hide-details></v-text-field>
            </template>
            <template v-slot:email="{ item }">
              <v-text-field v-model="item.email" dense outlined hide-details></v-text-field>
            </template>
          </v-data-table>

          <v-alert v-else :value="true" color="warning" icon="mdi-alert-circle-outline">
            Nenhum resultado encontrado.
          </v-alert>

          <v-dialog v-model="addAlunoDialog" max-width="500px">
            <v-card>
              <v-card-title>
                Adicionar Aluno
              </v-card-title>
              <v-card-text>
                <v-form ref="form">
                  <v-text-field v-model="RA" label="RA" outlined></v-text-field>
                  <v-text-field v-model="nome" label="Nome" outlined></v-text-field>
                  <v-text-field v-model="cpf" label="CPF" outlined></v-text-field>
                  <v-text-field v-model="email" label="Email" outlined></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn color="primary" @click="addAluno">Salvar</v-btn>
                <v-btn @click="addAlunoDialog = false">Cancelar</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>
