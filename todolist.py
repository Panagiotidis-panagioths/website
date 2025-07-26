def main():
    tasks = []

    while True :
        print ("\n === to-do list ===")
        print("1. add task")
        print("2. show tasks")
        print("3. mark as done ")
        print("4. exit ")
        choice=input("enter your choice : ")
         
        if choice == "1" :
            print()
            n_tasks = int(input("how many tasks you wanna add ? "))

            for i in range(n_tasks):
                task = input("enter task : ")
                tasks.append({"task":task, "done":False })
                print("Task added !")
        
        elif choice == "2" :
            print ("\nTasks:")
            for index , task in enumerate (tasks):
                status = "Done" if task["done"] else "Not done"
                print(f"{index + 1 }. {task['task']} - {status}")


        elif choice == "3" :
            task_index = int(input("enter the task number mark as done : "))
            if 0 <= task_index < len(tasks):
                tasks[task_index]["done"] = True
                print("Task marked as done ! ")
            else :
                print("Invalid task number .")

        elif choice == "4" : 
            print("exiting to-do list !!")
            break
        else:
            print("invalid choice . please try again !")

if __name__ =="__main__":
    main()