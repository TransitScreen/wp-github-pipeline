pipeline.directive('formToggleBtn', [function() {

    function link(scope, element, attr){

        var targets, climbnodes, searchWithin;
        var openText = ( scope.opentext ) ? scope.opentext : "&uarr; Less";
        var closedText = ( scope.closedtext ) ? scope.closedtext : "&darr; More";

        if (scope.mytarget) { //Target specified
            if (scope.climbnodes) { //how many nodes to climb up before searching down?
                searchWithin = element;
                for(var i=0; i<scope.climbnodes; i++) {
                    searchWithin = searchWithin.parent();//careful this returns a jQLite object
                }
                searchWithin = searchWithin[0];
            } else {
                searchWithin = document;
            }
            targets = findTargets( searchWithin, scope.mytarget );
        
        } else { //No target attribute. Default to sister
            if (element[0].nextSibling) { //if no nextSibling, try previous
                targets = element[0].nextSibling;
            } else {
                targets = element[0].previousSibling;
            }
        }
        
        /* 
         * Search within provided DOM element
         * Return an array of all matches
         */
        function findTargets(searchWithin, selector) {
            nl = searchWithin.querySelectorAll( selector );
            //results we want are a NodeList[n]
            // we convert to array for consistency
            var targets = [];
            for(var i = nl.length; i--; targets.unshift(nl[i]));
            return targets;
        }

        function toggleOpen(event){

            //regularize singletons as array so we can always loop...
            targets = ( targets.constructor === Array ) ? targets : [ targets ];

            element[0].innerHTML = (scope.open) ? openText : closedText ;
            scope.open = !scope.open;

            for (var i=0; i<targets.length; i++) {
                subTargets = targets[i];
                subTargets = ( subTargets.constructor === Array ) ? subTargets : [ subTargets ];
                for (var j=0; j<subTargets.length; j++) {
                    //toggle display property
                    subTargets[j].style.display = subTargets[j].style.display === 'none' ? '' : 'none';
                }
            }

        }
                
        element.on('click', function(event){ toggleOpen(event);event.preventDefault(); } );

        //start closed
        scope.open = false;
        toggleOpen();

    }

    return {
        restrict: 'A',
        //template: 'XXX',
        link: link,
        scope: {
          mytarget: "@",
          climbnodes: "=",
          opentext: "@",
          closedtext: "@"
        }
    };
}]);
