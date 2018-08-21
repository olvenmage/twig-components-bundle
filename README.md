# twig-components-bundle
A lightweight symfony bundle that provides easy ways to implement a modular, component structure into your twig templating.

# Usage
     composer require olveneer/twig-components-bundle
**The class**

Create a service implementing either NamedTwigComponentInterface or TwigComponentInterface, 
we'll get to the difference of those in a minute.

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

When using TwigComponentInterface, this is the only method you'll need, 
a function returning the parameters for your twig component. What are '$props', you ask? Well, component's main advantage
is that they can be reused multiple times. However, rarely do you ever need the 100% exact same component, so props
function as options to make your parameters more dynamic. Confused? Don't worry, it will all get more clear later.

When using NamedTwigComponentInterface you will also have the getName() method included:

    /**
     * Returns the string to use as a name for the component.
     *
     * @return String
     */
    public function getName()
    {
        return 'testComponent';
    }

What is the name for  a component? When rendering the component, or wanting to receive data from one, it's name is used
to reference it. When using TwigComponentInterface instead of NamedTwigComponent, the component still gets a name, except
that it's automatically generated.

Let's say the get_class() on your component results in App/Component/TestComponent, how we determine the name is:
take the last part of the class name, so in this case, TestComponent. Next, we make it camelcased, so it will be
'testComponent'.

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
 
 **The template**

When you have configured your component class it's time to create the template. The location of the template should be
in templates/components/<component_name>.html.twig. (You will get an error message stating this too).

the '/components' part is configurable in the config file, but we will get to that later.

What should the name of the file be? The component name of course! so in our case `testComponent.html.twig`.

Inside of here you can access all the parameters you send back.

Great! Now how do I get this template on my screen you ask? Simple!

There are two ways of doing it.

Method A: render it with a twig function in your template. <br>
Method B: render it from your controller/service.

Let's look at method A first.

Inside any template, you can use the following syntax:

`component($componentName, $props = [])` <br>
To render the component there while passing on optional props.

The name for this function is also configurable, again, we'll get to that.

Method B is, injecting the `TwigComponentKernel` into your service or controller and either call the 
`renderComponent($componentName, $props = [])` function if you just want to get the html, or the 
`renderView($componentName, $props = [])` to immediately get the response for the browser.

example:

    /**
     * @Route(path="/")
     * @param TwigComponentKernel $kernel
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function test(TwigComponentKernel $kernel)
    {
        return $kernel->renderView(TestComponent::class);
    }
    
Note that in the example I used `TestComponent::class` to retrieve the component instead of the component name, this is
an alternative way to render or retrieve the component. The 'normal' way is via

`$kernel->renderView('testComponent');`

**The config**

Now we finally get to the config, I will show you an example configuration file. <br>
    
    // config/packages/olveneer_twig_components.yaml
    
    olveneer_twig_components:
        components_directory: '/components'
        templating:
            render_function: 'render'
            access_function: 'access'

You might notice that besides the already known render funciton, there is also an access function stated, this 
function can be used to retrieve parameters from components inside the template, two quick examples:

        {{ component('secondComponent', {test: 2}) }}
        
        {% set params = access('secondComponent', ['asd', 'kernel'], { test: 2 })  %}
        
        {{ params['asd'] }}
        {{ params['kernel'] }}
        
        {{ access('secondComponent', 'asd') }} {# prints the value for the asd paramter #}
        
From this example you can see the component function being used, as well as the access function:

`access($componentName, $parameters, $props)`

as you might have noticed, the access function is dynamic, if you pass $parameters as a string, you will get just one
parameter, if you pass an array, you get all of the keys present in the array.
            




