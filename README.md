[![Total Downloads](https://img.shields.io/packagist/dt/olvenmage/twig-components-bundle.svg?style=flat-square)](https://packagist.org/packages/jonmldr/grumphp-doctrine-task)

# twig-components-bundle
A lightweight symfony bundle that provides easy ways to implement a modular, component structure into your twig templating.

# About
When developping front-end, I found myself quickly using frameworks like Vue and React. Recently I've focussed more on back-end programming in Symfony and quickly found myself missing the component structure Vue offered. Sure you had blocks and all, buit those were still lacking certain core functionalities that Vue did offer in it's own way. 


So I made this bundle to get that functionality back and I think it works out perfectly. And suggestions or feedback is always welcome!
olvenmage@live.nl

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
Let's say the `get_class()` on your component results in `App/Component/TestComponent`. We then take the class name without the namespace so in this case `TestComponent` next, we make it snake cased so the final name results in `test_component` 

If you want a custom name, use: <br>

    /**
     * Returns the string to use as a name for the component.
     *
     * @return String
     */
    public function getName()
    {
        return 'another_name';
    }

Now, these services are not automatically registered as Twig Components, you need to register the service with an extra
`olveneer.component` tag. example:

    app.test_component:
            class: App\Component\TestComponent
            tags: ['olveneer.component']

If you use autoconfigure, this happens automatically to any service extending the `TwigComponent` class!
 
 This takes every service from the Component directory and marks it as a Twig Component.
 
 **Rendering**

When you have configured your component class it's time to create the template. The location of the template should be
in `templates/components/<component_name>.html.twig`. (You will get an error message stating this too).

the `'/components'` part is configurable in the config file, but we will get to that later.

What should the name of the file be? The component name of course! so in our case `test_component.html.twig`.

Inside of here you can access all the parameters you send back.

Great! Now how do I get this template on my screen you ask? Simple!

There are two ways of doing it.

Method A: render it with a twig function in your template. <br>
Method B: render it from your controller/service.

Let's look at method A first.

Inside any template, you can use the following syntax:

`{% get name %} {% endget %}` <br>
This renders the component without passing props, to do that, use this:

`{% get name with { someProp: 'someValue' } %} {% endget %}` <br>

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

There are two types of {% slot %} tags. First, the ones that live inside {% get %} tags. The html between these tags
gets inserted into the component that's being rendered. Second, the {% slot %} tag that `doesn't` live in a {% get %} tag.
These tags define a place where HTML/Twig content can be slotted in, with the value in between being the default value.

Let's say you have a component named 'card' using the template `card.html.twig` file which renders a bootstrap card.

    // card.html.twig
    
    <div class="card"> 
        <div class="card-header">
            {% slot header %}
                 <h1> default header value </h1>
             {% endslot %}
        </div>
        <div class="card-body">
            {% slot body %}
                 <h1> default body value </h1>
             {% endslot %}
        </div>
    </div>
    
If you're familiar with Vue or React you're probably realising what's going on. This component is asking
for certain blocks of markup. The value you see inbetween the slot blocks is a default value if nothing
is `slotted` in.

Now we want to use this card component in one of our templates:

    // index.html.twig
    
    <body>
        {% get card %}
            {% slot header %}
                <div> My own html! </div>
            {% endslot %}
        {% endget %}
    </body>
    
So this will result in the rendering of the mainComponent where the header slot will be filled in
with `<div> My own html! </div>` and the body defaulting to `<h1> default body value </h1>`.

We can of course also slot our body in if we want to like this:

    // index.html.twig
    
    <body>
        {% get card %}
            {% slot header %}
                <div> My own html! </div>
            {% endslot %}
            
            {% slot body %}
                <div> A cool body </div>
            {% endslot %}
        {% endget %}
    </body>
    
Not only can you pass your values from the {% get %} to the component, the {% slot %} can also `expose` certain variables. Here is an example:

    // parent.html.twig
    {% for item in items %}
        ...
    {% endfor %}
    
    {% slot body expose { products: items, hello: 'Hi!!!' } %} {% endslot %}
    
    //child.html.twig
    {% get parent %}
        {% slot body %}
            <div> ... </div>
            <h1> {{ hello }} </h1>
            
            {% for product in products %}
                ...
            {% endfor %}
        {% endslot  %}
    {% endget %}
    
As you can see, the variables you expose can be used in the {% slot %} tags! Note,
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

The mixin has to be registered in your `services.yaml` with the `olveneer.mixin` tag. 
If you use autoconfigure, this happens automatically to any service extending the `TwigComponentMixin` class!

**Best Practices**

A couple of best practices for the best long-term update support and overall code optimization.

* Use the default `getName()` as much as possible (returning the short class name).
* Use the `configureProps()` method if applicable.
* Use the earlier stated auto configuring of the components with some specific configuring for edge-cases.
* Use snake case for custom component names. ex: `some_component_name`
* Create two seperate folders for your Mixins and Components under /src.

**The config**

Now we finally get to the config, I will show you an example configuration file. <br>
    
    // config/packages/olveneer_twig_components.yaml
    
    olveneer_twig_components:
        components_directory: '/components'
            
            
**What about just Embed?**
Some of you might think, "Why not use twig's embed?".

Well, there are some noticable differences:

An embed uses block tags isntead of our cool slot tags, the difference there is, a block can only be overriden once, which is logical since a block is quite static. However, a slot tag can expose variables for the overriding tag to use, so they can be very dynamic. For that reason, this works:

     // parent.html.twig
     <table>
          <tr>
               <th></th>
               <th></th>
          </tr>
          <tr>
               {% for item in items %}
                    {% slot body expose {item: item} %}
                    {% endslot %}
               {% endfor %}
          </tr>
     </table>

     // child.html.twig

     {% get parent %}
          {% slot body %}
               <td> {{ item.name }} </td>
               <td> {{ item.text }} </td>
          {% endslot %}
     {% endget %}
     
Note: This example is inspired from vuetify. Where they use this a lot in their components.
     
Also, component's can be very independent. When using embed you have to pass the parameters they require in the with {}. Which could lead to you having to inject and pass them along into every template where you wish to embed the template. With components, they can handle this themselves and just ask for `props` to use.

# Upcoming
* Open for suggestions!



