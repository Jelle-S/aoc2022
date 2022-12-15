# Revisit

For things that come to mind after I've written the first thing that came to my mind.

## Day 14

### Alternative idea

"Like, just calculate it bro".
In all seriousness, we should be able to 'just' calculate it:
- Draw a pyramid (get the triangular number) from the starting point to the bottom.
- Substract the length of every vertical line.
- Substract the pyramid value (aka triangular number) of every horizontal line.
  - For part one you can base the triangular number on the grid's width (pyramid can only be the width of the grid)
  - For part two you can base the triangular number on the grid's height (pyramid can only be the height of the grid)
  - Come to think of it, a "pyramid"'s width and height are always the same, but still, in part one we are limited by a fixed width, and we'll have to add a square of everything below
- The formula for a triangular number is `T(n) = (n*(n+1))/2`

**EDIT**

These aren't actually traingular numbers (which would be 1+2+3+...), these are (1+3+5+7...) for which I don't know the name or formula yet).

**EDIT2**

I'm still not sure what to call the numbers, but it has someting to do with [Gnomon](https://en.wikipedia.org/wiki/Gnomon_(figure)). And the formula is just to square it... Which also makes me think of something with bitwise operators or whatever... Not sure how it all fits...

### Caveats

- Don't count line intersections twice
- Horizontal lines or vertical lines that pass (intersect) the edges of the pyramid will change the shape of the pyramid.

## Day 15

Solution to puzzle 2 was correct but slow. There must be a better way...