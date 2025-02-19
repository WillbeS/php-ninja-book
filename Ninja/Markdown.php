<?php

namespace Ninja;

class Markdown {
  public function __construct(private string $string) {
  }

  public function toHtml() {
    // remove any HTML characters and convert to UTF-8
    $text = htmlspecialchars($this->string, ENT_QUOTES, 'UTF-8');

    // strong (bold)
    $text = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);

    // emphasis (italic)
    $text = preg_replace('/_([^_]+)_/', '<em>$1</em>', $text);
    $text = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $text);

    // Convert Windows (\r\n) to Unix (\n)
    $text = str_replace("\r\n", "\n", $text);
    // Convert Macintosh (\r) to Unix (\n)
    $text = str_replace("\r", "\n", $text);

    // Paragraphs
    $text = '<p>' . str_replace("\n\n", '</p><p>', $text) . '</p>';
    // Line breaks
    $text = str_replace("\n", '<br>', $text);

    // links
    //$text = preg_replace('/\[([^\]]+)]\((.+)\)/i', '<a href="$2">$1</a>', $text);

    // I think this was in the old edition and forgottent but will use it just in case
    $text = preg_replace(
         '/\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\)/i',
         '<a href="$2">$1</a>', $text);
    

    return $text;
  }
}