  //
  function processing_stop(id) {
    var pjs = Processing.getInstanceById(id);
    pjs.noLoop();
  }

  function processing_start(id) {
    var pjs = Processing.getInstanceById(id);
    pjs.loop();
  }

 