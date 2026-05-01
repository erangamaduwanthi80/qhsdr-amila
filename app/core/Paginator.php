<?php

class Paginator {

    public $page;
    public $perPage;
    public $total;
    public $totalPages;
    public $offset;

    public function __construct($total, $page = 1, $perPage = 25) {
        $this->total      = max(0, (int)$total);
        $this->perPage    = max(1, (int)$perPage);
        $this->totalPages = max(1, (int)ceil($this->total / $this->perPage));
        $this->page       = max(1, min((int)$page, $this->totalPages));
        $this->offset     = ($this->page - 1) * $this->perPage;
    }

    public static function fromRequest($total, $perPage = 25) {
        $page = (int)($_GET['page'] ?? 1);
        return new self($total, $page, $perPage);
    }

    public function hasPrev()       { return $this->page > 1; }
    public function hasNext()       { return $this->page < $this->totalPages; }
    public function rangeStart()    { return $this->total === 0 ? 0 : $this->offset + 1; }
    public function rangeEnd()      { return min($this->offset + $this->perPage, $this->total); }

    public function url($page, $extraQs = []) {
        $qs = array_merge($extraQs, ['page' => max(1, min($page, $this->totalPages))]);
        return '?' . http_build_query($qs);
    }

    public function render($extraQs = []) {
        if ($this->totalPages <= 1) return '';

        // Build a compact range: [1] ... [p-2 p-1 p p+1 p+2] ... [last]
        $window = 2;
        $pages = [];
        $start = max(1, $this->page - $window);
        $end   = min($this->totalPages, $this->page + $window);
        if ($start > 1) {
            $pages[] = 1;
            if ($start > 2) $pages[] = '...';
        }
        for ($i = $start; $i <= $end; $i++) $pages[] = $i;
        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) $pages[] = '...';
            $pages[] = $this->totalPages;
        }

        $extraQs = array_diff_key($extraQs, array_flip(['page']));
        $html = '<nav><ul class="pagination pagination-sm mb-0">';

        $html .= '<li class="page-item ' . ($this->hasPrev() ? '' : 'disabled') . '">'
              . '<a class="page-link" href="' . htmlspecialchars($this->url($this->page - 1, $extraQs)) . '">'
              . '<i class="bi bi-chevron-left"></i></a></li>';

        foreach ($pages as $p) {
            if ($p === '...') {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            } else {
                $active = $p === $this->page ? 'active' : '';
                $html .= '<li class="page-item ' . $active . '">'
                      . '<a class="page-link" href="' . htmlspecialchars($this->url($p, $extraQs)) . '">' . $p . '</a></li>';
            }
        }

        $html .= '<li class="page-item ' . ($this->hasNext() ? '' : 'disabled') . '">'
              . '<a class="page-link" href="' . htmlspecialchars($this->url($this->page + 1, $extraQs)) . '">'
              . '<i class="bi bi-chevron-right"></i></a></li>';

        $html .= '</ul></nav>';
        return $html;
    }
}
