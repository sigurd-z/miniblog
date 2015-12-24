{% macro render(post) %}

  <a class="post-title-link" href="{{ url_for(post.path) }}" itemprop="url">
  <article class="post post-type-{{ post.type | default('normal') }}" itemscope itemtype="http://schema.org/Article" {% if post.photos.length>0 %}style="background:url('{{ post.photos[0] }}')  0 20% / 100% auto no-repeat;"{% endif %} >
    <header class="post-header">

      <h1 class="post-title">

          <span itemprop="name">{{ post.title | default(__('post.untitled')) }}</span>

      </h1>

      <div class="post-meta">
        <time class="post-time" itemprop="dateCreated">
          {{ post.size }}
        </time>
      </div>

    </header>
  </article>
  </a>

{% endmacro %}
