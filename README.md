# WPMB Toolkit

**_In development. Not ready for anything._**

An extensible toolkit for WordPress devs and webmasters.

## Status

1. The logger is ready for testing.
2. ToolLoader - started.

## Problem solved

I often have a million little unrelated tweaks I want to make to a website.

What do you do?

- Create a million tiny plugins?
- Dump them all in 1 plugin?

I'm a proud member of team "dump them in 1 plugin". The toolkit is being build to formalize and streamline that process. It's a mini plugin manager.

- Install only the tools you want
- Enable/disable tools
- Requirements system
- Shared, streamline options page generator built on Custom Fields

## Tools

There are 2 types of tools.

The only exception is the logging function. Logging is an essential part of my development process, so I've fully integrated a basic logger outside the tool system. It will be improved to support additional integration or optional deactivation.

## Testing

There are currently no tests.

## To Do

- Tool activations: why do I have the data in the tool and in an array?
  - Is there a benefit to this?
  - If not, pick 1 method and refactor to it.
- - The admin messages aren't showing. It's such a low priority that I'm not going worry about it. (Tool Manager)
