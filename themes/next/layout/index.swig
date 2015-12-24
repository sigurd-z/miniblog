{% extends '_layout.swig' %}
{% import '_macro/post-block.swig' as post_template %}
{% import '_macro/sidebar.swig' as sidebar_template %}

{% block title %} {{ config.title }} {% endblock %}

{% block page_class %}
  {% if is_home() %} page-home {% endif %}
{% endblock %}

{% block content %}
  <style>
    .main-inner {width: 100%!important;}
  </style>

  <section id="posts" class="posts-waterfull clearfix">

    {% set posts_normal = page.posts %}

    {# Compatible with Hexo 2.* #}
    {% if page.posts.first().sticky >= 0 %}
      {# Extract sticky posts. #}
      {% if page.current === 1 %}
        {% set posts_sticky = site.posts.find({sticky: { '$gt': 0 }}) %}
        {% set posts_sticky = posts_sticky.sort('-sticky') %}
        {% for post in posts_sticky %}
          {{ post_template.render(post, true) }}
        {% endfor %}
      {% endif %}

      {% set posts_normal = page.posts.find({sticky: 0}) %}
    {% endif %}

    {% for post in posts_normal %}
      {{ post_template.render(post, true) }}
    {% endfor %}

  </section>

  {% include '_partials/pagination.swig' %}
{% endblock %}

{% block sidebar %}
  {{ sidebar_template.render(false) }}
{% endblock %}
