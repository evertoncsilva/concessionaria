<?php
    class AutomoveisPaginator {
        protected $totalItens;
        protected $numPaginas;
        protected $itensPorPagina;
        protected $paginaAtual;

         public function __construct($totalItens, $itensPorPagina, $paginaAtual)
         {
             $this->totalItens = $totalItens;
             $this->itensPorPagina = $itensPorPagina;
             $this->paginaAtual = $paginaAtual;

             $this->atualizaNumPaginas();
         }

         protected function atualizaNumPaginas()
         {
            $this->numPaginas = ($this->itensPorPagina == 0 ? 0 : (int) ceil($this->totalItens/$this->itensPorPagina));
         }

    }
?>