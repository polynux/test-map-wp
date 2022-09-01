// define module that gets filtered
class dfModule {
  constructor(moduleParts, moduleIndex, classList) {
    this.moduleParts = moduleParts; // array of jQuery objects (modules of column or columns of row)
    this.moduleIndex = moduleIndex;
    this.classList = classList;
  }
}

class dfFilterArea {
  constructor(elementSelector, filterElementType, multiSelectMode, cutSetMode, moduleArray) {
    this.selectorElementType = elementSelector;
    this.filterElementType = filterElementType;
    this.multiSelectMode = multiSelectMode;
    this.cutSetMode = cutSetMode;  // picky filtering, only filter Schnittmenge and not union, only show if efc-1 and efc-2 are in the element and not just one of both 

    this.moduleArray = moduleArray; // all HTML from modules

    this.activeFilterBtnClasses = []; // all active filter classes from button
  }
}

// array that contains HTML modules
var dfAllModules = [];
var filterableModulesArrayBuffer = [];

// multiple array
var dfAllFilters = [];      // efn-1 first element

var filterElementType = "column";
var selectorElementType = "";

var filterNumber;
var filterNumberSelector = ""; // efn-1 or empty if only 1 filter

var multiSelectMode = false;
var cutSetMode = false;

/**
 * write value to filterNumberSelector, filterElementType, selectorElementType 
 * @param {jQuery Object[]} button - jQuery object
 **/
function getMeTheData($button) {

  // get filternumber
  filterNumber = getFilterNumber($button);

  // get filternumberselector
  if(filterNumber == null) {
    filterNumber = 1;
    filterNumberSelector = " ";
  }
  else {
    filterNumberSelector = ".efn-" + filterNumber;
  }

  // check if this filter area is already defined
  var runOnce = false;
  if(dfAllFilters[filterNumber] !== undefined) {
    runOnce = true;
  }

  if(runOnce) {
    selectorElementType = dfAllFilters[filterNumber].selectorElementType;
    filterElementType = dfAllFilters[filterNumber].filterElementType;

    multiSelectMode = dfAllFilters[filterNumber].multiSelectMode;

    cutSetMode = dfAllFilters[filterNumber].cutSetMode;
  }
  else {
    // set selectorelementtype
    selectorElementType = ".ef-area > .elementor-container > .elementor-column";
    filterElementType = "column";


    multiSelectMode = false;
    cutSetMode = false;

  }
}

/**
 * remove classes and get classList
 * @param {jQuery Object[]} element - jQuery object of row or column
 **/
function removeAndGet($element) {

  var classList = $element.attr('class').split(/\s+/);

  classListFiltered = jQuery.grep(classList, function (className, index) {

    var returnValue = 0;  // 1 = take class with module

    // remove if starts not with et and not with dfs- classes
    if ((className.indexOf("elementor-") !== 0) && ((className.indexOf("dfs-") !== 0))) {      
      $element.removeClass(className);
      returnValue = 1;
    }

    return returnValue
  });

  return classListFiltered;

}

/**
 * get and remove element index class df-elementindex-
 * @param {jQuery Object[]} element - jQuery object of row or column
 **/
function getAndRemoveElementIndex($element) {

  // get classList as array
  var classListAsArray = $element.attr('class').split(/\s+/);
  var moduleIndexClass = jQuery.grep(classListAsArray, function (value, index) {
    return value.indexOf("df-elementindex-") === 0; // true if item should stay
  });

  var moduleIndex;
  if(moduleIndexClass.length == 1) {  //only one df-elementindex class
    var moduleIndexAsString = moduleIndexClass[0].substring(16);

    moduleIndex = parseInt(moduleIndexAsString);
    // remove df-elementindex- class
    $element.removeClass(moduleIndexClass[0]);
  }
  else if(moduleIndexClass.length == 0) {
    moduleIndex = -1;         // no element in that column
  }
  else(
    console.log("Elementor Filter Plugin: Error 563 More than one or no moduleIndex class")
  )
  return moduleIndex;
}


/**
 * get filter classes of button
 * @param {jQuery Object[]} button - jQuery object of button
 **/
function getFilterClassesOfButton($button) {
  // GET FILTER CLASSES
  var filterClassListButton = $button.attr('class').split(/\s+/);
  // remove all classes except efc-
  filterClassListButton = jQuery.grep(filterClassListButton, function (element) {
    return element.indexOf("efc-") === 0;
  });
  // remove button class
  var filterClassesOfButton = jQuery.grep(filterClassListButton, function (element) {
    return element.indexOf("ef-button") === -1;
  });
  return filterClassesOfButton;
}


/**
 * get filter number of ef-area
 * @param {jQuery Object[]} buttonOrArea - jQuery object of button
 **/
function getFilterNumber($buttonOrArea) {

  var filterNumber = null;
  var classBuffer 

  if( $buttonOrArea.hasClass("ef-button") ) {

    // get classes of row
    var classBuffer = $buttonOrArea.closest(".elementor-container").attr('class');

  }
  else {

    // get classes of area
    var classBuffer = $buttonOrArea.attr('class');

  }

  // get number of efn-X class
  if (classBuffer !== undefined) {
    var classList = classBuffer.split(/\s+/);

    classListFiltered = jQuery.each(classList, function (index, className) {
  
      if ((className.indexOf("efn-") == 0)) {
        var stringFilterNumber = className.substring(4);
        filterNumber = parseInt(stringFilterNumber, 10);
      }
  
    });
  }

  return filterNumber;
}


/**
 * filter elements
 * @param {jQuery Object[]} button - jQuery object of button
 **/
function doThis($button){

  // check if this filter area is already defined
  var runOnce = false;
  if(dfAllFilters[filterNumber] !== undefined) {
    runOnce = true;
  }

  // get all HTML columns or modules
  jQuery(selectorElementType).each(function(i, elementColumn){

    var moduleIndex = getAndRemoveElementIndex(jQuery(this));

    if(moduleIndex != -1) { // only if column had a moduleIndex class otherwise skip
      // add inner content of column to array
      var childrenOfElement = [];

      jQuery(this).children().each(function () {
        childrenOfElement.push(jQuery(this).detach());
      });

      if(runOnce) {
        // overwrite modules
        dfAllFilters[filterNumber].moduleArray[moduleIndex].moduleParts = childrenOfElement;
        removeAndGet(jQuery(this));
      }
      else {
        dfAllModules[moduleIndex] = new dfModule(childrenOfElement, moduleIndex, removeAndGet(jQuery(this)));
      }
    }
  });

  // create new filter Area
  if(!runOnce) {

    allModulesWithoutReference = dfAllModules.map(a => Object.assign({}, a));
    dfAllFilters[filterNumber] = new dfFilterArea(selectorElementType, filterElementType, multiSelectMode, cutSetMode, allModulesWithoutReference);

  }

  // empty filterableModulesArrayBuffer
  filterableModulesArrayBuffer.splice(0, filterableModulesArrayBuffer.length);

  // copy allModulesArray
  filterableModulesArrayBuffer = dfAllFilters[filterNumber].moduleArray.map(a => Object.assign({}, a));

  if(dfAllFilters[filterNumber].multiSelectMode) {

    var activeFilterBtnClasses = dfAllFilters[filterNumber].activeFilterBtnClasses;

    if($button.hasClass("ef-activebutton")) {
      toRemove = getFilterClassesOfButton($button);
      // remove elements from array from another
      activeFilterBtnClasses = activeFilterBtnClasses.filter( function( el ) {
        return !toRemove.includes( el );
      });

      $button.removeClass('ef-activebutton');
    }
    else {

      activeFilterBtnClasses = activeFilterBtnClasses.concat(getFilterClassesOfButton($button));
      
      $button.addClass('ef-activebutton');
    }

    if(dfAllFilters[filterNumber].cutSetMode) {
      filterCutSet(activeFilterBtnClasses);
    } 
    else{
      filterUnion(activeFilterBtnClasses);
    }

    dfAllFilters[filterNumber].activeFilterBtnClasses = activeFilterBtnClasses;

  }
  else {
    var filterClassesOfButton = getFilterClassesOfButton($button);

    if(filterClassesOfButton.length > 0) {
      var arrayOfIndexesForDeletion = [];
    
      jQuery.each(filterableModulesArrayBuffer, function(indexOfFilterModuleArray, moduleValue) {
        var containsSameFilterClass = 0;
    
        if (moduleValue.classList !== undefined) {
    
          jQuery.each(moduleValue.classList, function(index, filterClassModule){
            jQuery.each(filterClassesOfButton, function( index, filterClassButton ) { 
    
              if(filterClassModule == filterClassButton){
                containsSameFilterClass = 1;
              }
            });
          })
        }
        else {
          containsSameFilterClass = 0;
        }
        // if classes havent matched once, add to delete list
        if(containsSameFilterClass == 0) {
          arrayOfIndexesForDeletion.push(indexOfFilterModuleArray);      
        }
      });
      
    
      // delete filtered elements
      arrayOfIndexesForDeletion.sort(function(a, b){return b-a}); // sorts by desc order, arrayOfIndexesForDeletion.reverse(); would be also enough
      jQuery.each(arrayOfIndexesForDeletion, function(index, arrayPosition) {
        filterableModulesArrayBuffer.splice(arrayPosition, 1);
      })
    }

    
  }
  
  for (let filterableModuleIndex = 0; filterableModuleIndex < filterableModulesArrayBuffer.length; filterableModuleIndex++) {

    // add df-elementindex CLASS TO COLUMNS
    var moduleIndexAsString = filterableModulesArrayBuffer[filterableModuleIndex].moduleIndex.toString().padStart(3, "0");
    jQuery(selectorElementType + ":eq(" + filterableModuleIndex + ")").addClass("df-elementindex-" + moduleIndexAsString);

    // add all other classes to columns
    jQuery.each(filterableModulesArrayBuffer[filterableModuleIndex].classList, function( index, className ) {
      jQuery(selectorElementType + ":eq(" + filterableModuleIndex + ")").addClass(className);
    });

    // add inner row content
    jQuery.each(filterableModulesArrayBuffer[filterableModuleIndex].moduleParts, function( index, value ) {
      jQuery(selectorElementType + ":eq(" + filterableModuleIndex + ")").append(filterableModulesArrayBuffer[filterableModuleIndex].moduleParts[index]);
    });

  }

}

jQuery(function(jQuery) {

  // add comment
  jQuery(".ef-area").eq(0).before( "<!-- Filtering done with Elementor Filter Plugin, get it for free: https://danielvoelk.de/en/elementor-filter/ -->" );

  if(jQuery(".ef-area > .elementor-container > .elementor-column").length > 10) {
    alert("Elementor Filter: More than 10 columns. Please upgrade your license.")
  }
  else {
    // add elementindex- class to elements
    jQuery(".ef-area > .elementor-container > .elementor-column").each(function(moduleIndex, moduleOrColumn){

      var moduleIndexAsString = moduleIndex.toString().padStart(3, "0");
      jQuery(this).addClass("df-elementindex-" + moduleIndexAsString);
  
    })
  }

  // add class to show elements
  jQuery(this).addClass("ef-loaded");

 

  // on every click
  jQuery(".ef-button").on('click', function(event) {

    // remove url redirect
    event.preventDefault();

    getMeTheData(jQuery(this));

    doThis(jQuery(this));

  });
    

});