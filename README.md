# twig-components-bundle
A lightweight symfony bundle that provides easy ways to implement a modular, component structure into your twig templating.

# Installation
     composer require olveneer/twig-components-bundle
     
# Usage
**The class**

Each component requires a special service extending the `TwigComponent` class or implementing the `TwigComponentInterface`.

     /**
      * Returns the parameters to be used when rendering the template.
      * Props can be provided when rendering the component to make it more dynamic.
      *
      * @param array $props
      * @return array
      */
     public function getParameters(array $props)
     {
         return [
           'test' => 123
         ];
     }
     
    
a function returning the parameters for your twig component. What are these `$props`, you ask? Well, a component's main advantage is that they can be reused multiple times. However, rarely do you ever need the 100% exact same component, so props function as options to make your parameters more dynamic. Confused? Don't worry, it will all get more clear later.

     /**
      * Configures the props using the Symfony OptionResolver
      *
      * @param OptionsResolver $resolver
      */
     public function configureProps(OptionsResolver $resolver)
     {
        $resolver->setDefaults(['someProp' => 'someValue']);
     }

The `configureProps` method can be used to make sure your component always functions the way it should. 
This shouldn't be anything new if you've been using symfony for a while.

Let's determine the name of our component: 
Let's say the `get_class()` on your component results in `App/Component/TestComponent`, how the component's name is determined:
take the last part of the class name, so in this case, TestComponent. Next, we make it camelcased, so it will be
'testComponent' (this is also called the `short class name`). If you want a custom name, use: <br>

    /**
     * Returns the string to use as a name for the component.
     *
     * @return String
     */
    public function getName()
    {
        return 'testComponent';
    }

Now, these services are not automatically registered as Twig Components, you need to register the service with an extra
`olveneer.component` tag. example:

    app.test_component:
            class: App\Component\TestComponent
            tags: ['olveneer.component']

This can of course get quite tedious, so I advice you to use this simple life-hack.

First, create a separate directory from your Service folder for your components (you can see in the above example I
 called mine 'Component'). The reason for this is the following; symfony allows for dynamic service tagging, 
 controllers use it too.
 
 Just add this to your services.yaml:
 
        App\Component\:
                  resource: '../src/Component'
                  tags: ['olveneer.component']
 
 This takes every service from the Component directory and marks it as a Twig Component.
 
 **Rendering**

When you have configured your component class it's time to create the template. The location of the template should be
in `templates/components/<component_name>.html.twig`. (You will get an error message stating this too).

the `'/components'` part is configurable in the config file, but we will get to that later.

What should the name of the file be? The component name of course! so in our case `testComponent.html.twig`.

Inside of here you can access all the parameters you send back.

Great! Now how do I get this template on my screen you ask? Simple!

There are two ways of doing it.

Method A: render it with a twig function in your template. <br>
Method B: render it from your controller/service.

Let's look at method A first.

Inside any template, you can use the following syntax:

`{% get 'name' %} {% endget %}` <br>
This renders the component without passing props, to do that, use this:

`{% get 'name' with { someProp: 'someValue' } %} {% endget %}` <br>

Method B is, injecting the `Component` into your service or controller and either call the 
`renderComponent( $props = [])` function if you just want to get the html, or the 
`render($props = [])` to immediately get the response for the browser.

example:

    /**
     * @Route(path="/")
     * @param TestComponent $testComponent
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function test(TestComponent $testComponent)
    {
        return $testComponent->render();
    }
    
`$component->render($props = []);`

**The slot system**

TwigComponents also have the build-in functionality to pass html instead of just variables.

Let's say you have a component named 'card' using the template `card.html.twig` file which renders a bootstrap card.

    // card.html.twig
    
    <div class="card"> 
        <div class="card-header">
            {% collect 'header' %}
                 <h1> default header value </h1>
             {% endcollect %}
        </div>
        <div class="card-body">
            {% collect 'body' %}
                 <h1> default body value </h1>
             {% endcollect %}
        </div>
    </div>
    
If you're familiar with Vue or React you're probably realising what's going on. This component is asking
for certain blocks of markup. The value you see inbetween the collect blocks is a default value if nothing
is `supplied`.

Now we want to use this card component in one of our templates:

    // index.html.twig
    
    <body>
        {% get 'card' %}
            {% insert 'header' %}
                <div> My own html! </div>
            {% endinsert %}
        {% endget %}
    </body>
    
So this will result in the rendering of the mainComponent where the header slot will be filled in
with `<div> My own html! </div>` and the body defaulting to `<h1> default body value </h1>`.

We can of course also insert our body if we want to like this:

    // index.html.twig
    
    <body>
        {% get 'card' %}
            {% insert 'header' %}
                <div> My own html! </div>
            {% endinsert %}
            
            {% insert 'body' %}
                <div> A cool body </div>
            {% endinsert %}
        {% endget %}
    </body>
    
Not only can you pass your values from the insert to the collect, you can also `expose` certain variables. Here is an example:

    // parent.html.twig
    {% for item in items %}
        ...
    {% endfor %}
    
    {% collect 'body' exposes { products: items, hello: 'Hi!!!' } %} {% endcollect %}
    
    //child.html.twig
    {% get 'parent' %}
        {% insert 'body' %}
            <div> ... </div>
            <h1> {{ hello }} </h1>
            
            {% for product in products %}
                ...
            {% endfor %}
        {% endinsert  %}
    {% endget %}
    
As you can see, the variables you expose on the collect can be used in the {% insert %} tags. Note,
the variables you expose `only` exist inbetween those tags!

**Mixins**

Sometimes you have certain props or parameters that you like to use in most of your components. And to 
prevent duplicate code, you can use mixins like this:


    // SomeComponent.php
    
    public funciton getParameters()
    {
        ...
    }
    
    public function importMixins()
    {
        return [SomeMixin::class];
    }
    
As you can see, usage is incredibly easy. Now take a look at a mixin:


    // SomeMixin.php
    class SomeMixin extends TwigComponentMixin
    {
    
        /**
         * @return array
         *
         * Merges with the parameters.
         */
        public function getParameters()
        {
            return [];
        }
    
        /**
         * @return array
         *
         * Merges with the props.
         */
        public function getProps()
        {
            return [];
        }
    
        /**
         * @return int
         *
         * The execution order of all the mixins. Mixins with the same key override the earlier ones.
         * Lower goes first.
         */
        public function getPriority()
        {
            return 0;
        }
    }
    
A mixin is just another service, so inside of here you can inject and use whatever you like. When a mixin
is parsed, the parameters its return will be merged with the component's using `array_merge`.

The mixin has to be registered in your `services.yaml` with the `olveneer.mixin` tag. You can however use
the same trick earlier for mixins as well,Jjust add this to your services.yaml:

        App\Mixin\:
                  resource: '../src/Mixin'
                  tags: ['olveneer.mixin']
Now every mixin you define in the App\Mixin folder will be automatically registered as a mixin, neat!

**Best Practices**

A couple of best practices for the best long-term update support and overall code optimization.

* Use the default `getName()` as much as possible (returning the short class name).
* Use the `configureProps()` method if applicable.
* Use the earlier stated auto configuring of the components with some specific configuring for edge-cases.

**The config**

Now we finally get to the config, I will show you an example configuration file. <br>
    
    // config/packages/olveneer_twig_components.yaml
    
    olveneer_twig_components:
        components_directory: '/components'
            
# Upcoming
* Open for suggestions!



