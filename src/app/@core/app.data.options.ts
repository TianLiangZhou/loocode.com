export interface AppResponseDataOptions {
    code?: number;
    message?: string;
    data?: any[] | null | any;
}

export interface WindowContent {
    actionName?: string;
    mode: string;
}

export interface AppConfiguration {
  timestamp?: number;
  editor?: string;
  taxonomy: {name: string, value: string}[];
  post_type: {name: string, value: string}[];
  name?: string;
  logo?: string;
}

export interface AppMenuItem {
  class?: string;
  name?: string;
  url?: string;
  hidden?: boolean;
  sort?: number;
  link?: string;
  action?: string;
  children: AppMenuItem[];
  level: number;
  home?: boolean;
}

export interface Posts {
  id: number;
  title: string;
  termId: number;
  cover: string;
  origin: string;
  originLink: string;
  author: string;
  summary: string;
  content: string;
  views: number;
  status: number;
}

export interface SystemConfigure {
  option_id: number;
  option_name: string;
  option_value: any | any[];
  type: number;
  description?: string;
}

export interface Region {
  id: number;
  name: string;
  keyword: string;
  configure: any;
  weight: number;
  status: number;
  updated_time: string;
  created_time: string;
}

export interface Item {
  id: number;
  aid: number;
  title: string;
  subTitle: string;
  cover: string;
  coverSize: string;
  type: number;
  content: string;
  startTime: number;
  endTime: number;
  forceLogin: number;
  terminal: number;
  weight: number;
  bgColor: string;
  status: number;
}



export interface Manager {
  user_login?: string;
  ID: number;
  email: string;
  password: string;
  avatar: string;
  roles: number[];
}

export interface  Role {
  id?: number;
  name: string;
  permission: number[];
  createdDate?: string;
  updatedDate?: string;
}
