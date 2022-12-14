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

### Caveats

- Don't count line intersections twice
- Horizontal lines or vertical lines that pass (intersect) the edges of the pyramid will change the shape of the pyramid.
