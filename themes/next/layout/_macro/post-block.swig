{% macro render(post) %}

  {% if post.thumbnailrate>1.33 %}
    {% set stretch_direction='auto 200px' %}
  {% else %}
    {% set stretch_direction='266px auto' %}
  {% endif %}

  <a class="post-title-link" href="{{ url_for(post.path) }}" itemprop="url">
    <article class="post post-type-{{ post.type | default('normal') }}" itemscope itemtype="http://schema.org/Article" {% if post.thumbnail %}style="background:url('{{ post.thumbnail }}') 50% 0px / {{ stretch_direction }} no-repeat;"{% endif %} >
        <header class="post-header">
          <h1 class="post-title">
              <span itemprop="name">{{ post.title | default(__('post.untitled')) }}</span>
          </h1>
        </header>
    </article>
  </a>

{% endmacro %}
