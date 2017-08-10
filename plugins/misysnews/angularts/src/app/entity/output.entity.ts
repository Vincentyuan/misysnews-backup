

export class Bar {
  name : string;
  visibility : boolean;
}

export class FeedValue {
    id: string; // the name of current post under the group name
    postID:number; // with page => unique key
    title:string;
    category:string;
    feedURL: string;
    feedName: string; // the name of this feed
    layout: string;
    page: string;
    background: string;
    visibility : boolean;
    orderNumber: number; // equal to the nubmer of the index of this element in the array
    valid_time_from: string;
    valid_time_to: string;
    validStatus :boolean;
    editable :boolean;
    text : string;
    constructor(){
      this.validStatus = true;
      this.editable = true;
    }
}

export class FeedOutput {
    name: string; // the name of the feeds group
    values: FeedValue[];
    displayBackend:boolean;
    displayFrontend:boolean;
    constructor(){
      this.name = "";
      this.values = [];
      this.displayFrontend = true;
    }
}

export class RootObject {
    bar: {};
    feeds: FeedOutput[] = [];
    visibility : boolean = false;
    constructor(){
      this.bar = [];
      this.feeds = [];
    }
}
